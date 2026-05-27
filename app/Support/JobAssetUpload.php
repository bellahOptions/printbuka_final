<?php

namespace App\Support;

use App\Services\CloudinaryUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class JobAssetUpload
{
    /**
     * @param  array<int, array<string, mixed>>  $existing
     * @return array<int, array<string, mixed>>
     */
    public static function fromRequest(Request $request, array $existing = [], string $input = 'job_asset_files'): array
    {
        $assets = $existing;
        $uploader = $request->user();
        $isAdminUploader = $uploader !== null && $uploader->role !== 'customer' && $uploader->canAdmin('admin.view');

        $cloudinaryService = app(CloudinaryUploadService::class);

        if ($request->hasFile($input)) {
            foreach ((array) $request->file($input) as $file) {
                if (! $file || ! $file->isValid()) {
                    continue;
                }

                $fileMime = (string) ($file->getClientMimeType() ?? '');

                if (! $isAdminUploader && ! in_array($fileMime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                    throw ValidationException::withMessages([
                        $input => 'Only image uploads are allowed. Share PDF/SVG/ZIP files with external drive links.',
                    ]);
                }

                // Upload to both Cloudinary and local
                $result = $cloudinaryService->storeToBoth($file, 'job-assets', 'job-assets');

                $assets[] = $result;
            }
        }

        $livewireImagePathsInput = collect((array) $request->input('job_asset_image_paths'))
            ->filter(fn ($path): bool => is_string($path) && filled($path))
            ->unique()
            ->values()
            ->all();

        $livewireImagePaths = LivewireSecureUploads::consumePaths(
            $request,
            $livewireImagePathsInput,
            ['job-assets/images']
        );

        if ($livewireImagePathsInput !== [] && count($livewireImagePathsInput) !== count($livewireImagePaths)) {
            throw ValidationException::withMessages([
                'job_asset_image_paths' => 'One or more uploaded images are invalid or expired. Please upload the image again.',
            ]);
        }

        foreach ($livewireImagePaths as $path) {
            if (! Storage::disk('public')->exists($path)) {
                continue;
            }

            // For Livewire uploaded images (already stored locally), also push to Cloudinary
            $cloudinaryResult = null;
            if (CloudinaryUrl::isConfigured()) {
                $fullPath = Storage::disk('public')->path($path);
                $uploadResult = $cloudinaryService->upload($fullPath, ['folder' => 'job-assets/images']);
                if ($uploadResult['ok']) {
                    $cloudinaryResult = $uploadResult['public_id'];
                }
            }

            $assets[] = [
                'path' => $cloudinaryResult ?? $path,
                'name' => basename($path),
                'mime' => Storage::disk('public')->mimeType($path) ?: 'image/jpeg',
                'size' => Storage::disk('public')->size($path) ?: 0,
                'uploaded_at' => now()->toISOString(),
                'cloudinary_public_id' => $cloudinaryResult,
            ];
        }

        return $assets;
    }
}
