<?php

namespace Tests\Unit;

use App\Support\MediaUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    public function test_jpeg_and_png_uploads_are_automatically_stored_as_webp(): void
    {
        Storage::fake('public');

        foreach (['jpg', 'png'] as $extension) {
            $stored = MediaUpload::store(UploadedFile::fake()->image("sample.{$extension}", 800, 600));

            $this->assertSame('image/webp', $stored['mimetype']);
            $this->assertStringEndsWith('.webp', $stored['filepath']);
            Storage::disk('public')->assertExists($stored['filepath']);

            $bytes = Storage::disk('public')->get($stored['filepath']);
            $info = getimagesizefromstring($bytes);
            $this->assertSame('image/webp', $info['mime'] ?? null);
        }
    }

    public function test_existing_webp_is_stored_without_changing_its_format(): void
    {
        Storage::fake('public');
        $source = UploadedFile::fake()->image('existing.webp', 320, 240);

        $stored = MediaUpload::store($source);

        $this->assertSame('image/webp', $stored['mimetype']);
        $this->assertStringEndsWith('.webp', $stored['filepath']);
        Storage::disk('public')->assertExists($stored['filepath']);
    }
}
