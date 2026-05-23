<?php

namespace Tests\Unit;

use App\Support\MediaUrl;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUrlTest extends TestCase
{
    public function test_it_resolves_public_disk_paths_without_host_dependency(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('job-assets/sample.png', 'content');

        config(['filesystems.disks.public.url' => 'http://localhost/storage']);

        $resolved = MediaUrl::resolve('job-assets/sample.png');

        $this->assertSame('/storage/job-assets/sample.png', $resolved);
    }

    public function test_it_resolves_absolute_storage_app_public_paths(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('staff-photos/avatar.jpg', 'content');

        $absolutePath = storage_path('app/public/staff-photos/avatar.jpg');

        $resolved = MediaUrl::resolve($absolutePath);

        $this->assertSame('/storage/staff-photos/avatar.jpg', $resolved);
    }

    public function test_it_resolves_public_storage_prefixed_paths(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('product-images/featured/item.webp', 'content');

        $resolved = MediaUrl::resolve('/public/storage/product-images/featured/item.webp');

        $this->assertSame('/storage/product-images/featured/item.webp', $resolved);
    }

    public function test_it_keeps_external_urls_as_is(): void
    {
        $url = 'https://cdn.example.com/images/banner.png';

        $this->assertSame($url, MediaUrl::resolve($url));
    }
}
