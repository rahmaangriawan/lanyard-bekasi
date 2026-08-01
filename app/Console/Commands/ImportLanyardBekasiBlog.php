<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Post;
use App\Support\AuthorProfile;
use App\Support\HtmlSanitizer;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Imports the public lanyardbekasi.com blog without touching non-blog assets.
 *
 * The command deliberately has a dry-run default. Files are first downloaded
 * into a run-specific staging directory, then the database and active media
 * directory are swapped only after every source article has been validated.
 */
class ImportLanyardBekasiBlog extends Command
{
    protected $signature = 'blog:import-lanyardbekasi
        {--dry-run : Fetch and validate without changing database or active files}
        {--insecure : Disable TLS verification only for this local import run when PHP has no CA bundle}
        {--limit= : Limit source articles for a safe smoke test (requires --dry-run)}';

    protected $description = 'Import the public lanyardbekasi.com blog and prune only article-owned media.';

    private const BASE_URL = 'https://lanyardbekasi.com';
    private const ARCHIVE_PATH = '/blog';
    private const IMPORT_DIRECTORY = 'uploads/blog-import';

    private int $imageSuccessCount = 0;
    private int $archivePageCount = 0;
    private array $categoryNames = [];
    private bool $allowInsecure = false;

    public function handle(): int
    {
        $this->imageSuccessCount = 0;
        $this->archivePageCount = 0;
        $this->categoryNames = [];

        $dryRun = (bool) $this->option('dry-run');
        $this->allowInsecure = (bool) $this->option('insecure');
        $limit = $this->parseLimit();
        if ($limit !== null && ! $dryRun) {
            $this->error('--limit hanya boleh dipakai bersama --dry-run.');

            return self::FAILURE;
        }

        $author = AuthorProfile::primaryAuthor();
        if (! $author) {
            $this->error('Import dibatalkan karena belum ada akun ADMIN yang dapat dijadikan penulis blog.');

            return self::FAILURE;
        }

        try {
            $inventory = $this->collectArchiveInventory();
            $this->assertUniqueSlugs($inventory);
            if ($limit !== null) {
                $inventory = array_slice($inventory, 0, $limit);
            }

            $knownSlugs = array_fill_keys(array_column($inventory, 'slug'), true);
            $runId = Str::lower(Str::random(12));
            $stagingDirectory = self::IMPORT_DIRECTORY.'-staging-'.$runId;
            $transientOldDirectory = self::IMPORT_DIRECTORY.'-old-'.$runId;
            $prepared = [];

            foreach ($inventory as $source) {
                $prepared[] = $this->prepareArticle($source, $knownSlugs, $stagingDirectory, ! $dryRun);
                $this->line('Valid: '.$source['title']);
            }

            $this->validatePreparedArticles($prepared);
            $pruneManifest = $this->articleFileManifest();
            $this->renderSummary($prepared, $pruneManifest, false);

            if ($dryRun) {
                $this->info('Dry run selesai. Database dan filesystem tidak diubah.');
                return self::SUCCESS;
            }

            $this->swapActiveDirectory($stagingDirectory, $transientOldDirectory);
            $persisted = false;
            try {
                $this->persistReplacement($prepared, $author->id, $pruneManifest);
                $persisted = true;
                $this->pruneArticleFiles($pruneManifest, $transientOldDirectory);
                Storage::disk('public')->deleteDirectory($transientOldDirectory);
            } catch (Throwable $exception) {
                if (! $persisted) {
                    $this->restoreActiveDirectory($transientOldDirectory);
                }
                throw $exception;
            }

            $this->renderSummary($prepared, $pruneManifest, true);
            $this->info('Import Lanyard Bekasi selesai.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error('Import dibatalkan; data lama dipertahankan bila proses belum commit. '.$exception->getMessage());

            return self::FAILURE;
        }
    }

    private function parseLimit(): ?int
    {
        $value = $this->option('limit');
        if ($value === null || $value === false || $value === '') {
            return null;
        }

        $limit = (int) $value;
        return $limit > 0 ? $limit : null;
    }

    /** @return list<array<string, mixed>> */
    private function collectArchiveInventory(): array
    {
        $inventory = [];
        $page = 1;
        $totalPages = null;

        do {
            $url = self::BASE_URL.self::ARCHIVE_PATH.'?page='.$page;
            $html = $this->get($url)->body();
            $document = $this->document($html);
            $xpath = new DOMXPath($document);
            $body = $document->getElementsByTagName('body')->item(0);
            if (! $body) {
                throw new RuntimeException("Arsip blog halaman {$page} tidak memiliki body.");
            }

            $cards = $xpath->query('//main//article') ?: [];
            foreach ($cards as $card) {
                if (! $card instanceof DOMElement) {
                    continue;
                }

                $anchor = $this->firstElement($xpath, './/a[@href][.//h1 or .//h2 or .//h3]', $card)
                    ?: $this->firstElement($xpath, './/a[@href]', $card);
                $titleNode = $this->firstElement($xpath, './/h1|.//h2|.//h3', $card);
                $image = $this->firstElement($xpath, './/img[@src or @data-src]', $card);
                $time = $this->firstElement($xpath, './/time', $card);
                $category = $this->firstElement($xpath, './/a[contains(@href, "/category/")]', $card);

                $href = $anchor?->getAttribute('href') ?? '';
                $slug = $this->slugFromUrl($href);
                $title = $this->decodeText($titleNode?->textContent ?? '');
                $imageUrl = $this->absoluteUrl($image?->getAttribute('data-src') ?: $image?->getAttribute('src') ?: '');
                if ($slug === '' || $title === '' || $imageUrl === null) {
                    continue;
                }

                $inventory[] = [
                    'slug' => $slug,
                    'title' => $title,
                    'url' => self::BASE_URL.'/'.$slug,
                    'featuredUrl' => $imageUrl,
                    'categoryName' => $this->decodeText($category?->textContent ?? ''),
                    'categorySlug' => $this->slugFromUrl($category?->getAttribute('href') ?? ''),
                    'createdAt' => $this->parseDate($time?->getAttribute('datetime') ?: ''),
                    'archiveDate' => $this->decodeText($time?->textContent ?? ''),
                ];
            }

            $totalPages = $this->pageCount($xpath, $page);
            $this->archivePageCount++;
            $page++;
        } while ($page <= $totalPages);

        $inventory = collect($inventory)->unique('slug')->values()->all();
        if ($inventory === []) {
            throw new RuntimeException('Tidak ada artikel valid di arsip live.');
        }

        return $inventory;
    }

    private function pageCount(DOMXPath $xpath, int $currentPage): int
    {
        $pages = [];
        foreach ($xpath->query('//main//a[@href]') ?: [] as $anchor) {
            if (! $anchor instanceof DOMElement) {
                continue;
            }
            $query = parse_url($anchor->getAttribute('href'), PHP_URL_QUERY);
            parse_str((string) $query, $params);
            if (isset($params['page']) && ctype_digit((string) $params['page'])) {
                $pages[] = (int) $params['page'];
            }
        }

        $pages[] = $currentPage;
        return max($pages ?: [1]);
    }

    /** @param list<array<string, mixed>> $posts */
    private function assertUniqueSlugs(array $posts): void
    {
        $slugs = [];
        foreach ($posts as $post) {
            $slug = (string) ($post['slug'] ?? '');
            if ($slug === '' || isset($slugs[$slug])) {
                throw new RuntimeException('Slug artikel live kosong atau duplikat: '.$slug);
            }
            $slugs[$slug] = true;
        }
    }

    /** @param array<string, mixed> $source @param array<string, bool> $knownSlugs */
    private function prepareArticle(array $source, array $knownSlugs, string $stagingDirectory, bool $writeFiles): array
    {
        $html = $this->get((string) $source['url'])->body();
        $document = $this->document($html);
        $xpath = new DOMXPath($document);
        $prose = $this->firstElement($xpath, '//main//div[contains(concat(" ", normalize-space(@class), " "), " prose ")]', $document);
        if (! $prose) {
            $prose = $this->firstElement($xpath, '//main//article', $document);
        }
        if (! $prose) {
            throw new RuntimeException('Area isi artikel tidak ditemukan: '.$source['slug']);
        }

        $content = $this->innerHtml($prose);
        $content = $this->cleanArticleHtml($content);
        $content = $this->normalizeContentHeadings($content);
        $content = $this->rewriteArticleLinks($content, $knownSlugs);
        $downloads = [];

        $contentImages = $this->extractImageUrls($content);
        foreach ($contentImages as $remoteUrl) {
            $downloads[$remoteUrl] = $this->downloadImage(
                $remoteUrl,
                $source['slug'].'-inline-'.substr(sha1($remoteUrl), 0, 10),
                $stagingDirectory,
                $writeFiles,
            );
        }

        $downloads[$source['featuredUrl']] ??= $this->downloadImage(
            $source['featuredUrl'],
            $source['slug'].'-featured',
            $stagingDirectory,
            $writeFiles,
        );

        foreach ($downloads as $remoteUrl => $download) {
            $content = str_replace([$remoteUrl, htmlspecialchars($remoteUrl, ENT_QUOTES | ENT_HTML5, 'UTF-8')], $download['url'], $content);
        }

        $content = $this->normalizeImageAttributes($content);
        $content = HtmlSanitizer::clean($content);
        if (trim(strip_tags($content)) === '' || $this->containsRemoteImage($content)) {
            throw new RuntimeException('Konten hasil import tidak valid: '.$source['slug']);
        }

        $title = $this->decodeText((string) $source['title']);
        $createdAt = $source['createdAt'] instanceof Carbon ? $source['createdAt'] : now();
        $description = Str::limit($this->decodeText(strip_tags($content)), 155, '');
        $categoryName = $source['categoryName'] !== '' ? $source['categoryName'] : 'Artikel';
        $categorySlug = $source['categorySlug'] !== '' ? $source['categorySlug'] : 'artikel';
        $this->categoryNames[$categorySlug] = $categoryName;

        return [
            'title' => $title,
            'slug' => $source['slug'],
            'content' => $content,
            'featuredImage' => $downloads[$source['featuredUrl']]['url'],
            'createdAt' => $createdAt,
            'updatedAt' => $createdAt,
            'metaTitle' => $title,
            'metaDescription' => $description,
            'tags' => null,
            'categoryName' => $categoryName,
            'categorySlug' => $categorySlug,
            'media' => array_values($downloads),
        ];
    }

    private function cleanArticleHtml(string $html): string
    {
        $document = $this->document($html);
        $xpath = new DOMXPath($document);
        $body = $document->getElementsByTagName('body')->item(0);
        if (! $body) {
            return '';
        }

        foreach (['script', 'style', 'form', 'iframe', 'noscript', 'svg', 'nav', 'aside', 'header', 'footer'] as $tag) {
            $this->removeNodes($xpath->query('.//'.$tag, $body));
        }

        foreach ($xpath->query('.//*', $body) ?: [] as $element) {
            if (! $element instanceof DOMElement) {
                continue;
            }
            $marker = strtolower($element->getAttribute('class').' '.$element->getAttribute('id'));
            if (preg_match('/(?:^|[\s_-])(toc|table-of-contents|breadcrumb|sidebar|related|share|author|comment|pagination|newsletter)(?:[\s_-]|$)/', $marker)) {
                $element->parentNode?->removeChild($element);
            }
        }

        return $this->innerHtml($body);
    }

    /** @return list<string> */
    private function extractImageUrls(string &$html): array
    {
        $document = $this->document($html);
        $xpath = new DOMXPath($document);
        $body = $document->getElementsByTagName('body')->item(0);
        $urls = [];
        if (! $body) {
            return [];
        }

        foreach ($xpath->query('.//img', $body) ?: [] as $image) {
            if (! $image instanceof DOMElement) {
                continue;
            }
            $source = $image->getAttribute('data-src')
                ?: $image->getAttribute('data-lazy-src')
                ?: $image->getAttribute('data-original')
                ?: $image->getAttribute('src');
            if ($source === '' || str_starts_with($source, 'data:')) {
                $source = $this->firstSrcsetCandidate($image->getAttribute('srcset'));
            }
            $absolute = $this->absoluteUrl($source);
            if ($absolute === null) {
                throw new RuntimeException('Elemen gambar artikel tidak memiliki URL HTTP.');
            }
            $image->setAttribute('src', $absolute);
            $urls[] = $absolute;
        }

        $html = $this->innerHtml($body);
        return array_values(array_unique($urls));
    }

    private function firstSrcsetCandidate(string $srcset): string
    {
        $candidate = trim((string) (preg_split('/\s*,\s*/', $srcset)[0] ?? ''));
        return trim((string) (preg_split('/\s+/', $candidate, 2)[0] ?? ''));
    }

    /** @param array<string, bool> $knownSlugs */
    private function rewriteArticleLinks(string $html, array $knownSlugs): string
    {
        $document = $this->document($html);
        $xpath = new DOMXPath($document);
        $body = $document->getElementsByTagName('body')->item(0);
        if (! $body) {
            return $html;
        }

        foreach ($xpath->query('.//a[@href]', $body) ?: [] as $anchor) {
            if (! $anchor instanceof DOMElement) {
                continue;
            }
            $href = html_entity_decode($anchor->getAttribute('href'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $host = strtolower((string) parse_url($href, PHP_URL_HOST));
            if ($host !== '' && ! in_array($host, ['lanyardbekasi.com', 'www.lanyardbekasi.com'], true)) {
                continue;
            }
            $path = trim((string) parse_url($href, PHP_URL_PATH), '/');
            $segments = array_values(array_filter(explode('/', $path)));
            $last = (string) end($segments);
            $candidate = Str::slug($last);
            $fragment = parse_url($href, PHP_URL_FRAGMENT);

            if (isset($knownSlugs[$candidate])) {
                $anchor->setAttribute('href', '/blog/'.$candidate.($fragment ? '#'.$fragment : ''));
            } elseif (($segments[0] ?? '') === 'category' && $candidate !== '') {
                $anchor->setAttribute('href', '/kategori/'.$candidate);
            } elseif ($path === 'blog' || $path === '') {
                $anchor->setAttribute('href', '/blog');
            }
        }

        return $this->innerHtml($body);
    }

    private function normalizeImageAttributes(string $html): string
    {
        $document = $this->document($html);
        $xpath = new DOMXPath($document);
        $body = $document->getElementsByTagName('body')->item(0);
        if (! $body) {
            return $html;
        }

        foreach ($xpath->query('.//img', $body) ?: [] as $image) {
            if (! $image instanceof DOMElement) {
                continue;
            }
            foreach (['srcset', 'sizes', 'data-src', 'data-lazy-src', 'data-original'] as $attribute) {
                $image->removeAttribute($attribute);
            }
            $image->setAttribute('loading', 'lazy');
            $image->setAttribute('decoding', 'async');
        }

        return $this->innerHtml($body);
    }

    private function normalizeContentHeadings(string $html): string
    {
        $document = $this->document($html);
        $xpath = new DOMXPath($document);
        $body = $document->getElementsByTagName('body')->item(0);
        if (! $body) {
            return $html;
        }

        foreach (iterator_to_array($xpath->query('.//h1 | .//h2 | .//h3', $body) ?: []) as $heading) {
            if (! $heading instanceof DOMElement) {
                continue;
            }
            $replacementTag = match (strtolower($heading->tagName)) {
                'h1' => 'h2',
                'h2' => 'h3',
                default => 'h4',
            };
            $replacement = $document->createElement($replacementTag);
            foreach (iterator_to_array($heading->attributes) as $attribute) {
                $replacement->setAttribute($attribute->name, $attribute->value);
            }
            while ($heading->firstChild) {
                $replacement->appendChild($heading->firstChild);
            }
            $heading->parentNode?->replaceChild($replacement, $heading);
        }

        return $this->innerHtml($body);
    }

    /** @return array{filename:string,filepath:string,mimetype:string,size:int,url:string} */
    private function downloadImage(string $url, string $basename, string $stagingDirectory, bool $writeFile): array
    {
        $response = $this->get($url);
        $body = $response->body();
        $contentType = strtolower(trim(explode(';', (string) $response->header('Content-Type'))[0]));
        if (! str_starts_with($contentType, 'image/') || $body === '' || @getimagesizefromstring($body) === false) {
            throw new RuntimeException("File gambar tidak valid: {$url}");
        }

        $extension = $this->extensionFromContentType($contentType) ?? $this->extensionFromUrl($url) ?? 'jpg';
        $filename = Str::slug($basename).'.'.$extension;
        $relative = self::IMPORT_DIRECTORY.'/'.$filename;
        if ($writeFile && ! Storage::disk('public')->put($stagingDirectory.'/'.$filename, $body)) {
            throw new RuntimeException("Tidak dapat menulis gambar staging: {$filename}");
        }

        $this->imageSuccessCount++;
        return [
            'filename' => $filename,
            'filepath' => 'storage/'.$relative,
            'mimetype' => $contentType,
            'size' => strlen($body),
            'url' => '/storage/'.$relative,
        ];
    }

    /** @param list<array<string, mixed>> $articles */
    private function validatePreparedArticles(array $articles): void
    {
        foreach ($articles as $article) {
            if (trim(strip_tags((string) $article['content'])) === '') {
                throw new RuntimeException('Konten hasil sanitasi kosong: '.$article['slug']);
            }
            foreach ($article['media'] as $media) {
                if (! str_starts_with((string) $media['url'], '/storage/'.self::IMPORT_DIRECTORY.'/')) {
                    throw new RuntimeException('URL media lokal tidak valid: '.$article['slug']);
                }
            }
        }
    }

    /** @return array{files:list<string>,media:list<string>,ambiguous:list<string>} */
    private function articleFileManifest(): array
    {
        $files = [];
        $media = [];
        $ambiguous = [];
        foreach (Post::query()->get(['featuredImage', 'content']) as $post) {
            $values = [(string) $post->featuredImage, (string) $post->content];
            foreach ($values as $value) {
                if (preg_match_all('#/storage/'.preg_quote(self::IMPORT_DIRECTORY, '#').'/[^\s"\'<>)]+' . '#i', $value, $matches)) {
                    foreach ($matches[0] as $path) {
                        $relative = ltrim(parse_url($path, PHP_URL_PATH) ?: '', '/');
                        if ($this->isReferencedOutsideBlog($relative)) {
                            $ambiguous[] = $relative;
                            continue;
                        }
                        if (! in_array($relative, $files, true)) {
                            $files[] = $relative;
                        }
                    }
                }
            }
        }

        foreach (Media::query()->where(function ($query): void {
            $query->where('filepath', 'like', 'storage/'.self::IMPORT_DIRECTORY.'/%')
                ->orWhere('url', 'like', '/storage/'.self::IMPORT_DIRECTORY.'/%');
        })->pluck('filepath') as $path) {
            $relative = ltrim((string) $path, '/');
            if ($this->isReferencedOutsideBlog($relative)) {
                $ambiguous[] = $relative;
                continue;
            }
            if (! in_array($relative, $files, true)) {
                $files[] = $relative;
            }
            $media[] = (string) $path;
        }

        return ['files' => array_values(array_unique($files)), 'media' => array_values(array_unique($media)), 'ambiguous' => array_values(array_unique($ambiguous))];
    }

    private function isReferencedOutsideBlog(string $relative): bool
    {
        $needle = '/'.ltrim($relative, '/');
        $needleWithoutStorage = preg_replace('#^/storage/#', '/', $needle) ?: $needle;
        $sources = [
            'product' => ['featuredImage', 'specs', 'accessories', 'shortDescription', 'description'],
            'page' => ['content'],
            'citypage' => ['featuredImage', 'content'],
            'portfolio' => ['imageUrl', 'logoUrl', 'link'],
            'setting' => ['value'],
        ];

        foreach ($sources as $table => $columns) {
            foreach (DB::table($table)->get($columns) as $row) {
                foreach ($columns as $column) {
                    $value = (string) ($row->{$column} ?? '');
                    if ($value !== '' && (str_contains($value, $needle) || str_contains($value, $needleWithoutStorage))) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /** @param list<array<string, mixed>> $articles */
    private function persistReplacement(array $articles, int $authorId, array $pruneManifest): void
    {
        DB::transaction(function () use ($articles, $authorId, $pruneManifest): void {
            $categories = [];
            foreach ($this->categoryNames as $slug => $name) {
                $categories[$slug] = Category::updateOrCreate(
                    ['slug' => $slug, 'type' => 'BLOG'],
                    ['name' => $name, 'description' => 'Artikel Lanyard Bekasi'],
                );
            }

            Comment::query()->whereIn('postId', Post::query()->select('id'))->delete();
            Post::query()->delete();
            if ($pruneManifest['media'] !== []) {
                Media::query()->whereIn('filepath', $pruneManifest['media'])->delete();
            }

            foreach ($articles as $article) {
                $category = $categories[$article['categorySlug']]
                    ?? Category::updateOrCreate(
                        ['slug' => $article['categorySlug'], 'type' => 'BLOG'],
                        ['name' => $article['categoryName'], 'description' => 'Artikel Lanyard Bekasi'],
                    );
                Post::query()->create([
                    'title' => $article['title'],
                    'slug' => $article['slug'],
                    'content' => $article['content'],
                    'published' => true,
                    'featuredImage' => $article['featuredImage'],
                    'categoryId' => $category->id,
                    'authorId' => $authorId,
                    'tags' => $article['tags'],
                    'createdAt' => $article['createdAt'],
                    'updatedAt' => $article['updatedAt'],
                    'metaTitle' => $article['metaTitle'],
                    'metaDescription' => $article['metaDescription'],
                ]);

                foreach ($article['media'] as $media) {
                    Media::query()->updateOrCreate(['filepath' => $media['filepath']], $media);
                }
            }

            Category::query()
                ->where('type', 'BLOG')
                ->whereNotIn('slug', array_keys($this->categoryNames))
                ->whereDoesntHave('posts')
                ->whereDoesntHave('products')
                ->delete();
        });
    }

    private function pruneArticleFiles(array $manifest, string $oldDirectory): void
    {
        $disk = Storage::disk('public');
        foreach ($manifest['files'] as $file) {
            $prefix = 'storage/'.self::IMPORT_DIRECTORY.'/';
            if (str_starts_with($file, $prefix)) {
                $relative = substr($file, strlen($prefix));
                $disk->delete($oldDirectory.'/'.$relative);
            }
        }
    }

    private function swapActiveDirectory(string $stagingDirectory, string $oldDirectory): void
    {
        $disk = Storage::disk('public');
        if ($disk->exists(self::IMPORT_DIRECTORY)) {
            $this->copyDirectory(self::IMPORT_DIRECTORY, $oldDirectory);
            $disk->deleteDirectory(self::IMPORT_DIRECTORY);
        }
        try {
            $this->copyDirectory($stagingDirectory, self::IMPORT_DIRECTORY);
            $disk->deleteDirectory($stagingDirectory);
        } catch (Throwable $exception) {
            $this->restoreActiveDirectory($oldDirectory);
            throw new RuntimeException('Tidak dapat mengaktifkan folder gambar hasil import.', 0, $exception);
        }
    }

    private function restoreActiveDirectory(string $oldDirectory): void
    {
        $disk = Storage::disk('public');
        $disk->deleteDirectory(self::IMPORT_DIRECTORY);
        if ($disk->exists($oldDirectory)) {
            $this->copyDirectory($oldDirectory, self::IMPORT_DIRECTORY);
            $disk->deleteDirectory($oldDirectory);
        }
    }

    private function copyDirectory(string $source, string $destination): void
    {
        $disk = Storage::disk('public');
        foreach ($disk->allFiles($source) as $file) {
            $relative = ltrim(substr($file, strlen($source)), '/');
            if (! $disk->put($destination.'/'.$relative, $disk->get($file))) {
                throw new RuntimeException("Tidak dapat menyalin file {$file}.");
            }
        }
    }

    private function get(string $url): Response
    {
        $request = Http::timeout(30)
            ->retry(2, 350)
            ->withHeaders([
                'Accept' => 'text/html,image/avif,image/webp,image/*,*/*;q=0.8',
                'User-Agent' => 'LanyardBekasiBlogImporter/1.0',
            ]);
        if ($this->allowInsecure) {
            $request = $request->withoutVerifying();
        }
        $response = $request->get($url);
        if (! $response->successful()) {
            throw new RuntimeException("HTTP {$response->status()} saat mengambil {$url}");
        }
        return $response;
    }

    private function document(string $html): DOMDocument
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?><!doctype html><html><body>'.$html.'</body></html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        return $document;
    }

    private function innerHtml(DOMNode $node): string
    {
        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument?->saveHTML($child) ?? '';
        }
        return trim($html);
    }

    private function firstElement(DOMXPath $xpath, string $query, DOMNode $context): ?DOMElement
    {
        $node = $xpath->query($query, $context)?->item(0);
        return $node instanceof DOMElement ? $node : null;
    }

    private function removeNodes(mixed $nodes): void
    {
        if (! $nodes) {
            return;
        }
        foreach (iterator_to_array($nodes) as $node) {
            $node->parentNode?->removeChild($node);
        }
    }

    private function containsRemoteImage(string $html): bool
    {
        return preg_match('/<img\b[^>]+src=["\'][^"\']*https?:\/\//i', $html) === 1;
    }

    private function absoluteUrl(string $url): ?string
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        if ($url === '' || str_starts_with($url, 'data:')) {
            return null;
        }
        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }
        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }
        return self::BASE_URL.'/'.ltrim($url, '/');
    }

    private function slugFromUrl(string $url): string
    {
        $path = trim((string) parse_url(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'), PHP_URL_PATH), '/');
        $segments = array_values(array_filter(explode('/', $path)));
        $last = (string) end($segments);
        return Str::slug($last);
    }

    private function parseDate(string $value): ?Carbon
    {
        try {
            return $value !== '' ? Carbon::parse($value) : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function decodeText(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');
    }

    private function extensionFromContentType(string $contentType): ?string
    {
        return match ($contentType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/avif' => 'avif',
            default => null,
        };
    }

    private function extensionFromUrl(string $url): ?string
    {
        $extension = strtolower(pathinfo((string) parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'], true)
            ? ($extension === 'jpeg' ? 'jpg' : $extension)
            : null;
    }

    private function renderSummary(array $articles, array $manifest, bool $changed): void
    {
        $this->table(['Metric', 'Value'], [
            ['Halaman arsip', $this->archivePageCount],
            ['Artikel valid', count($articles)],
            ['Kategori live', count($this->categoryNames)],
            ['Gambar baru', $this->imageSuccessCount],
            ['File artikel lama teridentifikasi', count($manifest['files'])],
            ['Record media lama teridentifikasi', count($manifest['media'])],
            ['File ambigu dipertahankan', count($manifest['ambiguous'])],
            ['Data diubah', $changed ? 'Ya' : 'Tidak'],
        ]);
    }
}
