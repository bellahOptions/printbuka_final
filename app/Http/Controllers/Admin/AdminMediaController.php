<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CloudinaryUploadService;
use App\Support\CloudinaryUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminMediaController extends Controller
{
    /**
     * List previously uploaded images from Cloudinary, scoped to this app's
     * default folder, for the shared image-library picker.
     */
    public function index(Request $request): JsonResponse
    {
        if (! CloudinaryUrl::isConfigured()) {
            return response()->json(['ok' => false, 'images' => [], 'next_cursor' => null, 'message' => 'Cloudinary is not configured.']);
        }

        $defaultFolder = trim((string) config('cloudinary.default_folder', 'printbuka'), '/');

        $options = [
            'type' => 'upload',
            'prefix' => $defaultFolder,
            'max_results' => 30,
            'context' => true,
        ];

        if ($request->filled('cursor')) {
            $options['next_cursor'] = (string) $request->input('cursor');
        }

        try {
            $result = CloudinaryUrl::client()->adminApi()->assets($options);

            $images = collect((array) ($result['resources'] ?? []))
                ->map(fn (array $asset): array => [
                    'public_id' => (string) ($asset['public_id'] ?? ''),
                    'url' => (string) ($asset['secure_url'] ?? $asset['url'] ?? ''),
                    'width' => $asset['width'] ?? null,
                    'height' => $asset['height'] ?? null,
                    'format' => $asset['format'] ?? null,
                    'created_at' => $asset['created_at'] ?? null,
                ])
                ->values();

            return response()->json([
                'ok' => true,
                'images' => $images,
                'next_cursor' => $result['next_cursor'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            return response()->json([
                'ok' => false,
                'images' => [],
                'next_cursor' => null,
                'message' => 'Could not load the image library: '.$exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload a single image (optionally already cropped client-side) to
     * Cloudinary. Shared by the rich-text editor's image button and every
     * "Choose Image" picker across the admin portal.
     */
    public function store(Request $request, CloudinaryUploadService $uploadService): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:8192'],
            'folder' => ['nullable', 'string', 'max:100'],
        ]);

        $result = $uploadService->upload($validated['image'], [
            'folder' => $validated['folder'] ?? 'editor-uploads',
        ]);

        return response()->json($result, $result['ok'] ? 200 : 422);
    }
}
