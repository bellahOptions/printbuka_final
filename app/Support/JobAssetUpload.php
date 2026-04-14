<?php

namespace App\Support;

use Illuminate\Http\Request;

class JobAssetUpload
{
    /**
     * @param  array<int, array<string, mixed>>  $existing
     * @return array<int, array<string, mixed>>
     */
    public static function fromRequest(Request $request, array $existing = [], string $input = 'job_asset_files'): array
    {
        if (! $request->hasFile($input)) {
            return $existing;
        }

        $assets = $existing;

        foreach ((array) $request->file($input) as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $assets[] = [
                'path' => $file->store('job-assets', 'public'),
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_at' => now()->toISOString(),
            ];
        }

        return $assets;
    }
}
