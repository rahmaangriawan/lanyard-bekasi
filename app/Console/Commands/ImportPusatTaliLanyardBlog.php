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

class ImportPusatTaliLanyardBlog extends Command
{
    protected $signature = 'blog:import-pusattalilanyard
        {--dry-run : Fetch and validate without changing database or files}
        {--keep-old : Keep existing local posts instead of replacing them}
        {--limit= : Limit imported articles for testing (requires --dry-run or --keep-old)}';

    protected $description = 'Import the complete pusattalilanyard.com WordPress blog and its images into the local CMS.';

    private const BASE_URL = 'https://pusattalilanyard.com';

    private const POSTS_ENDPOINT = self::BASE_URL.'/wp-json/wp/v2/posts';

    private const IMPORT_DIRECTORY = 'uploads/blog-import';

    private int $imageSuccessCount = 0;

    /** @var array<int, array<string, mixed>> */
    private array $mediaById = [];

    /** @var array<int, string> */
    private array $tagNames = [];

    public function handle(): int
    {
        $this->imageSuccessCount = 0;
        $this->mediaById = [];
        $this->tagNames = [];

        $dryRun = (bool) $this->option('dry-run');
        $keepOld = (bool) $this->option('keep-old');
        $limit = $this->parseLimit();
        $primaryAuthor = AuthorProfile::primaryAuthor();

        if (! $primaryAuthor) {
            $this->error('Import dibatalkan karena belum ada akun ADMIN yang dapat dijadikan penulis blog.');

            return self::FAILURE;
        }

        if ($limit !== null && ! $dryRun && ! $keepOld) {
            $this->error('--limit hanya boleh dipakai bersama --dry-run atau --keep-old agar tidak menghapus sebagian besar artikel.');

            return self::FAILURE;
        }

        $this->info('Mengambil artikel melalui WordPress REST API '.self::POSTS_ENDPOINT);

        try {
            $sourcePosts = $this->collectSourcePosts();
            $this->tagNames = $this->collectTerms('tags');
            $this->assertUniqueSlugs($sourcePosts);
        } catch (Throwable $exception) {
            $this->error('Gagal mengambil inventory artikel: '.$exception->getMessage());

            return self::FAILURE;
        }

        if ($limit !== null) {
            $sourcePosts = array_slice($sourcePosts, 0, $limit);
        }

        $knownSlugs = array_fill_keys(array_column($sourcePosts, 'slug'), true);
        $runId = Str::lower(Str::random(12));
        $stagingDirectory = self::IMPORT_DIRECTORY.'-staging-'.$runId;
        $backupDirectory = self::IMPORT_DIRECTORY.'-backup-'.$runId;
        $prepared = [];

        try {
            foreach ($sourcePosts as $sourcePost) {
                $article = $this->prepareArticle($sourcePost, $knownSlugs, $stagingDirectory, ! $dryRun);
                $prepared[] = $article;
                $this->line('Valid: '.$article['title']);
            }

            if ($prepared === []) {
                throw new RuntimeException('Tidak ada artikel valid yang ditemukan.');
            }

            $this->validatePreparedArticles($prepared);

            if ($dryRun) {
                $this->info('Dry run selesai. Database dan filesystem tidak diubah.');
                $this->renderSummary($prepared, false, $keepOld);

                return self::SUCCESS;
            }

            if ($keepOld) {
                $this->mergeExistingFilesIntoStaging($stagingDirectory);
            }

            $this->swapImportDirectory($stagingDirectory, $backupDirectory);

            try {
                $this->persistArticles($prepared, $keepOld, $primaryAuthor->id);
            } catch (Throwable $exception) {
                $this->restoreImportDirectory($backupDirectory);
                throw $exception;
            }

            Storage::disk('public')->deleteDirectory($backupDirectory);
        } catch (Throwable $exception) {
            Storage::disk('public')->deleteDirectory($stagingDirectory);
            $this->error('Import dibatalkan; data lama dipertahankan. '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Import selesai.');
        $this->renderSummary($prepared, true, $keepOld);

        return self::SUCCESS;
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
    private function collectSourcePosts(): array
    {
        $posts = [];
        $page = 1;
        $total = null;
        $totalPages = 1;

        do {
            $response = $this->get(self::POSTS_ENDPOINT, [
                'per_page' => 100,
                'page' => $page,
                'orderby' => 'date',
                'order' => 'desc',
                '_fields' => 'id,date,modified,slug,link,title,content,excerpt,featured_media,categories,tags',
            ]);

            $payload = $response->json();
            if (! is_array($payload) || ! array_is_list($payload)) {
                throw new RuntimeException("Payload post halaman {$page} tidak valid.");
            }

            $total ??= (int) $response->header('X-WP-Total');
            $totalPages = max(1, (int) $response->header('X-WP-TotalPages'));
            $posts = array_merge($posts, $payload);
            $page++;
        } while ($page <= $totalPages);

        if ($total === null || $total < 1 || count($posts) !== $total) {
            throw new RuntimeException('Jumlah artikel API tidak lengkap: menerima '.count($posts).', seharusnya '.($total ?? 0).'.');
        }

        return array_values($posts);
    }

    /** @return array<int, string> */
    private function collectTerms(string $type): array
    {
        $terms = [];
        $page = 1;
        $totalPages = 1;

        do {
            $response = $this->get(self::BASE_URL.'/wp-json/wp/v2/'.$type, [
                'per_page' => 100,
                'page' => $page,
                '_fields' => 'id,name',
            ]);
            $totalPages = max(1, (int) ($response->header('X-WP-TotalPages') ?: 1));

            foreach ((array) $response->json() as $term) {
                if (isset($term['id'], $term['name'])) {
                    $terms[(int) $term['id']] = $this->decodeText((string) $term['name']);
                }
            }
            $page++;
        } while ($page <= $totalPages);

        return $terms;
    }

    /** @param list<array<string, mixed>> $posts */
    private function assertUniqueSlugs(array $posts): void
    {
        $slugs = [];
        foreach ($posts as $post) {
            $slug = Str::slug((string) ($post['slug'] ?? ''));
            if ($slug === '') {
                throw new RuntimeException('Artikel tanpa slug ditemukan.');
            }
            if (isset($slugs[$slug])) {
                throw new RuntimeException("Slug duplikat ditemukan: {$slug}.");
            }
            $slugs[$slug] = true;
        }
    }

    /**
     * @param array<string, mixed> $source
     * @param array<string, bool> $knownSlugs
     * @return array<string, mixed>
     */
    private function prepareArticle(array $source, array $knownSlugs, string $stagingDirectory, bool $writeFiles): array
    {
        $title = $this->decodeText((string) data_get($source, 'title.rendered', ''));
        $slug = Str::slug((string) ($source['slug'] ?? ''));
        $content = (string) data_get($source, 'content.rendered', '');

        if ($title === '' || $slug === '' || trim(strip_tags($content)) === '') {
            throw new RuntimeException('Artikel memiliki title, slug, atau content kosong: '.($slug ?: '(tanpa slug)'));
        }

        $content = $this->cleanArticleHtml($content);
        $content = $this->rewriteArticleLinks($content, $knownSlugs);
        $downloads = [];
        $contentImages = $this->extractContentImages($content);

        foreach ($contentImages as $index => $remoteUrl) {
            $downloads[$remoteUrl] = $this->downloadImage(
                $remoteUrl,
                $slug.'-content-'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                $stagingDirectory,
                $writeFiles,
            );
        }

        $featuredImage = null;
        $featuredId = (int) ($source['featured_media'] ?? 0);
        if ($featuredId > 0) {
            $media = $this->getMedia($featuredId);
            $remoteUrl = (string) ($media['source_url'] ?? '');
            if ($remoteUrl === '') {
                throw new RuntimeException("Featured image artikel {$slug} tidak memiliki source_url.");
            }
            $downloads[$remoteUrl] ??= $this->downloadImage(
                $remoteUrl,
                $slug.'-featured',
                $stagingDirectory,
                $writeFiles,
            );
            $featuredImage = $downloads[$remoteUrl]['url'];
        }

        foreach ($downloads as $remoteUrl => $download) {
            $content = str_replace([$remoteUrl, htmlspecialchars($remoteUrl, ENT_QUOTES)], $download['url'], $content);
        }

        $content = $this->normalizeImageAttributes($content);
        $content = HtmlSanitizer::clean($content);

        if ($this->containsRemoteImage($content) || $this->containsToc($content)) {
            throw new RuntimeException("Konten {$slug} masih berisi gambar remote atau TOC.");
        }

        $excerpt = $this->decodeText(strip_tags((string) data_get($source, 'excerpt.rendered', '')));
        $tagNames = collect((array) ($source['tags'] ?? []))
            ->map(fn (mixed $id) => $this->tagNames[(int) $id] ?? null)
            ->filter()
            ->unique()
            ->values()
            ->all();

        return [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'featuredImage' => $featuredImage,
            'createdAt' => $this->parseDate((string) ($source['date'] ?? '')) ?? now(),
            'updatedAt' => $this->parseDate((string) ($source['modified'] ?? '')) ?? now(),
            'metaTitle' => $title,
            'metaDescription' => Str::limit($excerpt !== '' ? $excerpt : strip_tags($content), 155, ''),
            'tags' => implode(', ', $tagNames),
            'media' => array_values($downloads),
        ];
    }

    /** @return array<string, mixed> */
    private function getMedia(int $id): array
    {
        if (! isset($this->mediaById[$id])) {
            $payload = $this->get(self::BASE_URL.'/wp-json/wp/v2/media/'.$id, [
                '_fields' => 'id,source_url,mime_type,media_details,alt_text',
            ])->json();
            if (! is_array($payload)) {
                throw new RuntimeException("Metadata media {$id} tidak valid.");
            }
            $this->mediaById[$id] = $payload;
        }

        return $this->mediaById[$id];
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
        $finalPath = self::IMPORT_DIRECTORY.'/'.$filename;

        if ($writeFile && ! Storage::disk('public')->put($stagingDirectory.'/'.$filename, $body)) {
            throw new RuntimeException("Tidak dapat menulis gambar staging: {$filename}");
        }

        $this->imageSuccessCount++;

        return [
            'filename' => $filename,
            'filepath' => 'storage/'.$finalPath,
            'mimetype' => $contentType,
            'size' => strlen($body),
            'url' => '/storage/'.$finalPath,
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
            if (preg_match('/(?:^|[\s_-])(toc|table-of-contents|elementor-toc|breadcrumb|sidebar|related|share|author|comment|pagination|newsletter)(?:[\s_-]|$)/', $marker)) {
                $element->parentNode?->removeChild($element);
            }
        }

        return $this->innerHtml($body);
    }

    /** @return list<string> */
    private function extractContentImages(string &$html): array
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
            $absolute = $this->absoluteUrl($source);
            if ($absolute === null) {
                throw new RuntimeException('Elemen gambar artikel tidak memiliki URL HTTP yang dapat diunduh.');
            }
            $image->setAttribute('src', $absolute);
            $urls[] = $absolute;
        }

        $html = $this->innerHtml($body);

        return array_values(array_unique($urls));
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
            $host = parse_url($href, PHP_URL_HOST);
            if ($host !== null && $host !== 'pusattalilanyard.com' && $host !== 'www.pusattalilanyard.com') {
                continue;
            }
            $path = trim((string) parse_url($href, PHP_URL_PATH), '/');
            $segments = array_values(array_filter(explode('/', $path)));
            $candidate = Str::slug((string) end($segments));
            if ($candidate !== '' && isset($knownSlugs[$candidate])) {
                $fragment = parse_url($href, PHP_URL_FRAGMENT);
                $anchor->setAttribute('href', '/blog/'.$candidate.($fragment ? '#'.$fragment : ''));
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

    private function mergeExistingFilesIntoStaging(string $stagingDirectory): void
    {
        $disk = Storage::disk('public');
        foreach ($disk->files(self::IMPORT_DIRECTORY) as $file) {
            $target = $stagingDirectory.'/'.basename($file);
            if (! $disk->exists($target)) {
                $disk->copy($file, $target);
            }
        }
    }

    private function swapImportDirectory(string $stagingDirectory, string $backupDirectory): void
    {
        $disk = Storage::disk('public');
        if ($disk->files(self::IMPORT_DIRECTORY) !== []) {
            $this->copyDirectory(self::IMPORT_DIRECTORY, $backupDirectory);
            $disk->deleteDirectory(self::IMPORT_DIRECTORY);
        }

        try {
            $this->copyDirectory($stagingDirectory, self::IMPORT_DIRECTORY);
            $disk->deleteDirectory($stagingDirectory);
        } catch (Throwable $exception) {
            $this->restoreImportDirectory($backupDirectory);
            throw new RuntimeException('Tidak dapat mengaktifkan folder gambar hasil import.');
        }
    }

    private function restoreImportDirectory(string $backupDirectory): void
    {
        $disk = Storage::disk('public');
        $disk->deleteDirectory(self::IMPORT_DIRECTORY);
        if ($disk->files($backupDirectory) !== []) {
            $this->copyDirectory($backupDirectory, self::IMPORT_DIRECTORY);
            $disk->deleteDirectory($backupDirectory);
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

    /** @param list<array<string, mixed>> $articles */
    private function persistArticles(array $articles, bool $keepOld, int $authorId): void
    {
        DB::transaction(function () use ($articles, $keepOld, $authorId): void {
            $category = Category::firstOrCreate(
                ['slug' => 'artikel', 'type' => 'BLOG'],
                ['name' => 'Artikel', 'description' => 'Artikel Pusat Tali Lanyard'],
            );

            if (! $keepOld) {
                Comment::query()->whereIn('postId', Post::query()->select('id'))->delete();
                Post::query()->delete();
                Media::query()
                    ->where('filepath', 'like', 'storage/'.self::IMPORT_DIRECTORY.'/%')
                    ->orWhere('url', 'like', '/storage/'.self::IMPORT_DIRECTORY.'/%')
                    ->delete();
            }

            foreach ($articles as $article) {
                Post::updateOrCreate(
                    ['slug' => $article['slug']],
                    [
                        'title' => $article['title'],
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
                    ],
                );

                foreach ($article['media'] as $media) {
                    Media::updateOrCreate(['filepath' => $media['filepath']], $media);
                }
            }
        });
    }

    private function get(string $url, array $query = []): Response
    {
        $response = Http::timeout(30)
            ->retry(2, 350)
            ->withHeaders([
                'Accept' => 'application/json,text/html,image/avif,image/webp,*/*;q=0.8',
                'User-Agent' => 'PusatTaliLanyardContentImporter/1.0',
            ])
            ->get($url, $query);

        if (! $response->successful()) {
            throw new RuntimeException("HTTP {$response->status()} saat mengambil {$url}");
        }

        return $response;
    }

    private function document(string $html): DOMDocument
    {
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8" ?><!doctype html><html><body>'.$html.'</body></html>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
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
        return preg_match('/<img\b[^>]+(?:src|srcset)=["\'][^"\']*https?:\/\//i', $html) === 1;
    }

    private function containsToc(string $html): bool
    {
        return preg_match('/(?:elementor-toc|table-of-contents|class=["\'][^"\']*\btoc\b|>\s*Daftar Isi\s*<)/i', $html) === 1;
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

    /** @param list<array<string, mixed>> $articles */
    private function renderSummary(array $articles, bool $changed, bool $keepOld): void
    {
        $this->table(['Metric', 'Value'], [
            ['Artikel valid', count($articles)],
            ['Gambar tervalidasi', $this->imageSuccessCount],
            ['Data diubah', $changed ? 'Ya' : 'Tidak'],
            ['Mode replace', $keepOld ? 'Tidak (--keep-old)' : 'Ya'],
        ]);
    }
}
