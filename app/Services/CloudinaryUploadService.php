<?php

namespace App\Services;

use App\Support\CloudinaryUrl;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryUploadService
{
    /**
     * Upload a file to Cloudinary.
     *
     * @param  UploadedFile|string  $file  UploadedFile instance or file path
     * @param  array<string, mixed>  $options  Additional Cloudinary upload options
     * @return array{ok: bool, public_id: string|null, url: string|null, message: string}
     */
    public function upload(
        UploadedFile|string $file,
        array $options = []
    ): array {
        if (! CloudinaryUrl::isConfigured()) {
            return [
                'ok' => false,
                'public_id' => null,
                'url' => null,
                'message' => 'Cloudinary is not configured. Set CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, and CLOUDINARY_API_SECRET.',
            ];
        }

        try {
            $client = CloudinaryUrl::client();
            $uploadApi = $client->uploadApi();

            $defaultFolder = trim((string) config('cloudinary.default_folder', 'printbuka'), '/');

            $uploadOptions = array_merge([
                'folder' => $defaultFolder.'/'.ltrim($options['folder'] ?? 'uploads', '/'),
                'resource_type' => 'image',
                'quality' => 'auto',
                'fetch_format' => 'auto',
                'use_filename' => true,
                'unique_filename' => true,
            ], $options);

            // Remove custom folder from $options to avoid duplication
            unset($uploadOptions['folder_custom']);

            $result = $uploadApi->upload($file, $uploadOptions);

            $publicId = (string) ($result['public_id'] ?? '');
            $url = (string) ($result['secure_url'] ?? '');

            return [
                'ok' => true,
                'public_id' => $publicId,
                'url' => $url,
                'message' => 'Upload successful.',
            ];
        } catch (\Throwable $e) {
            Log::error('Cloudinary upload failed: '.$e->getMessage(), [
                'file' => $file instanceof UploadedFile ? $file->getClientOriginalName() : $file,
                'error' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'public_id' => null,
                'url' => null,
                'message' => 'Upload failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Upload multiple files to Cloudinary.
     *
     * @param  array<int, UploadedFile>  $files
     * @param  array<string, mixed>  $options
     * @return array<int, array{ok: bool, public_id: string|null, url: string|null, message: string}>
     */
    public function uploadMany(array $files, array $options = []): array
    {
        $results = [];

        foreach ($files as $file) {
            $results[] = $this->upload($file, $options);
        }

        return $results;
    }

    /**
     * Delete a resource from Cloudinary by public_id.
     *
     * @param  string  $publicId  The Cloudinary public_id
     * @param  array<string, mixed>  $options
     * @return array{ok: bool, message: string}
     */
    public function delete(string $publicId, array $options = []): array
    {
        if (! CloudinaryUrl::isConfigured()) {
            return [
                'ok' => false,
                'message' => 'Cloudinary is not configured.',
            ];
        }

        try {
            $client = CloudinaryUrl::client();

            $client->uploadApi()->destroy($publicId, $options);

            return [
                'ok' => true,
                'message' => 'Deleted successfully.',
            ];
        } catch (\Throwable $e) {
            Log::error('Cloudinary delete failed: '.$e->getMessage(), [
                'public_id' => $publicId,
                'error' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'message' => 'Delete failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Store an uploaded file to both Cloudinary and local (fallback) disk.
     *
     * @param  UploadedFile  $file
     * @param  string  $localPath  Local storage path (e.g., "job-assets/images")
     * @param  string  $cloudFolder  Cloudinary folder (e.g., "job-assets/images")
     * @return array{path: string, name: string, mime: string, size: int, uploaded_at: string, cloudinary_public_id: string|null}
     */
    public function storeToBoth(UploadedFile $file, string $localPath = 'job-assets/images', string $cloudFolder = 'job-assets/images'): array
    {
        // Always store locally as fallback
        $localStoredPath = $file->store($localPath, 'public');

        $result = [
            'path' => $localStoredPath,
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType() ?: 'image/jpeg',
            'size' => $file->getSize(),
            'uploaded_at' => now()->toISOString(),
            'cloudinary_public_id' => null,
        ];

        // Try to upload to Cloudinary
        if (CloudinaryUrl::isConfigured()) {
            $uploadResult = $this->upload($file, ['folder' => $cloudFolder]);

            if ($uploadResult['ok']) {
                $result['path'] = $uploadResult['public_id'] ?? $localStoredPath;
                $result['cloudinary_public_id'] = $uploadResult['public_id'];
            }
        }

        return $result;
    }

    /**
     * Store an array of uploaded files using storeToBoth.
     *
     * @param  array<int, UploadedFile>  $files
     * @param  string  $localPath
     * @param  string  $cloudFolder
     * @return array<int, array{path: string, name: string, mime: string, size: int, uploaded_at: string, cloudinary_public_id: string|null}>
     */
    public function storeManyToBoth(array $files, string $localPath = 'job-assets/images', string $cloudFolder = 'job-assets/images'): array
    {
        $assets = [];

        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $assets[] = $this->storeToBoth($file, $localPath, $cloudFolder);
        }

        return $assets;
    }
}
