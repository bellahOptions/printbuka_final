<?php

namespace App\Livewire\Uploads;

use App\Models\SharedMediaAsset;
use App\Services\CloudinaryUploadService;
use App\Support\CloudinaryUrl;
use App\Support\LivewireSecureUploads;
use App\Support\MediaUrl;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class SecureImageUpload extends Component
{
    use WithFileUploads;

    public string $inputName = 'image_upload_path';

    public bool $multiple = false;

    public string $directory = 'image-uploads';

    public int $maxSizeKb = 2048;

    public int $maxFiles = 1;

    public ?int $minWidth = 80;

    public ?int $minHeight = 80;

    public mixed $upload = null;

    /**
     * @var array<int, mixed>
     */
    public array $uploads = [];

    public ?string $storedPath = null;

    /**
     * @var array<int, string>
     */
    public array $storedPaths = [];

    public ?string $accept = null;

    /**
     * Whether the stored path is a Cloudinary public_id.
     */
    public bool $isCloudinary = false;

    /**
     * True when the current single storedPath was picked from the shared
     * image library (or cropped via the library modal) rather than uploaded
     * fresh through this field. Such assets may be referenced elsewhere, so
     * they must never be auto-deleted from Cloudinary when this field is
     * replaced or cleared — only a freshly-owned upload is safe to delete.
     */
    public bool $storedPathFromLibrary = false;

    /**
     * @var array<string, bool> public_id => true for entries in $storedPaths
     *      that came from the shared library rather than a fresh upload.
     */
    public array $storedPathsFromLibrary = [];

    public function mount(
        string $inputName = 'image_upload_path',
        bool $multiple = false,
        string $directory = 'image-uploads',
        int $maxSizeKb = 2048,
        int $maxFiles = 1,
        ?int $minWidth = 80,
        ?int $minHeight = 80,
        ?string $initialPath = null,
        array $initialPaths = [],
        ?string $accept = null
    ): void {
        $this->inputName = $inputName;
        $this->multiple = $multiple;
        $this->directory = trim($directory, '/');
        $this->maxSizeKb = max(256, $maxSizeKb);
        $this->maxFiles = max(1, $maxFiles);
        $this->minWidth = $minWidth;
        $this->minHeight = $minHeight;
        $this->accept = $accept;
        $this->isCloudinary = CloudinaryUrl::isConfigured();

        if ($this->multiple) {
            $this->storedPaths = collect($initialPaths)
                ->filter(fn ($path): bool => is_string($path) && filled($path))
                ->unique()
                ->take($this->maxFiles)
                ->values()
                ->all();

            LivewireSecureUploads::registerMany(request(), $this->storedPaths);

            return;
        }

        $this->storedPath = filled($initialPath) ? (string) $initialPath : null;

        if (filled($this->storedPath)) {
            LivewireSecureUploads::register(request(), (string) $this->storedPath);
        }
    }

    public function updatedUpload(): void
    {
        if ($this->multiple || ! $this->upload) {
            return;
        }

        $this->resetErrorBag();

        $this->validate([
            'upload' => $this->fileRules(),
        ]);

        if (filled($this->storedPath) && ! $this->storedPathFromLibrary) {
            $this->deleteStoredPath((string) $this->storedPath);
        }

        if ($this->isCloudinary) {
            // Upload directly to Cloudinary
            $cloudinaryService = app(CloudinaryUploadService::class);
            $result = $cloudinaryService->storeToBoth($this->upload, $this->directory, $this->directory);
            $this->storedPath = $result['cloudinary_public_id'] ?? $result['path'];
        } else {
            $path = $this->upload->store($this->directory, 'public');
            $this->storedPath = $path;
        }

        $this->storedPathFromLibrary = false;
        LivewireSecureUploads::register(request(), (string) $this->storedPath);

        $this->upload = null;
    }

    /**
     * Attach an image that was picked from the shared Cloudinary library, or
     * cropped and uploaded via the library modal, without going through the
     * native file-upload flow. Never deletes the previous asset when it is
     * itself library-sourced, since it may still be in use elsewhere.
     */
    public function selectFromLibrary(string $publicId): void
    {
        if (! filled($publicId)) {
            return;
        }

        // From this point on, this asset is known to be (potentially)
        // referenced from more than one place and must never be silently
        // deleted from Cloudinary again, even by an unrelated field/session.
        SharedMediaAsset::query()->firstOrCreate(['public_id' => $publicId]);

        if ($this->multiple) {
            if (in_array($publicId, $this->storedPaths, true)) {
                return;
            }

            if (count($this->storedPaths) >= $this->maxFiles) {
                $this->addError('uploads', "You can select up to {$this->maxFiles} images.");

                return;
            }

            $this->storedPaths[] = $publicId;
            $this->storedPathsFromLibrary[$publicId] = true;
            LivewireSecureUploads::register(request(), $publicId);

            return;
        }

        if (filled($this->storedPath) && ! $this->storedPathFromLibrary) {
            $this->deleteStoredPath((string) $this->storedPath);
        }

        $this->storedPath = $publicId;
        $this->storedPathFromLibrary = true;
        $this->upload = null;
        LivewireSecureUploads::register(request(), $publicId);
    }

    public function updatedUploads(): void
    {
        if (! $this->multiple || $this->uploads === []) {
            return;
        }

        $this->resetErrorBag();

        $this->validate([
            'uploads' => ['array', 'max:'.$this->maxFiles],
            'uploads.*' => $this->fileRules(),
        ]);

        $cloudinaryService = app(CloudinaryUploadService::class);

        foreach ($this->uploads as $upload) {
            if (! $upload) {
                continue;
            }

            if (count($this->storedPaths) >= $this->maxFiles) {
                $this->addError('uploads', "You can upload up to {$this->maxFiles} images.");
                break;
            }

            if ($this->isCloudinary) {
                $result = $cloudinaryService->storeToBoth($upload, $this->directory, $this->directory);
                $newPath = $result['cloudinary_public_id'] ?? $result['path'];
            } else {
                $newPath = $upload->store($this->directory, 'public');
            }

            $this->storedPaths[] = $newPath;
            LivewireSecureUploads::register(request(), (string) $newPath);
        }

        $this->storedPaths = collect($this->storedPaths)
            ->filter(fn ($path): bool => is_string($path) && filled($path))
            ->unique()
            ->take($this->maxFiles)
            ->values()
            ->all();

        $this->uploads = [];
    }

    public function clearSingle(): void
    {
        if (! filled($this->storedPath)) {
            return;
        }

        if ($this->storedPathFromLibrary) {
            LivewireSecureUploads::forget(request(), (string) $this->storedPath);
        } else {
            $this->deleteStoredPath((string) $this->storedPath);
        }

        $this->storedPath = null;
        $this->storedPathFromLibrary = false;
        $this->upload = null;
    }

    public function removePath(string $encodedPath): void
    {
        $decodedPath = base64_decode($encodedPath, true);

        if (! is_string($decodedPath) || $decodedPath === '') {
            return;
        }

        $this->storedPaths = collect($this->storedPaths)
            ->reject(fn (string $path): bool => $path === $decodedPath)
            ->values()
            ->all();

        if ($this->storedPathsFromLibrary[$decodedPath] ?? false) {
            LivewireSecureUploads::forget(request(), $decodedPath);
        } else {
            $this->deleteStoredPath($decodedPath);
        }

        unset($this->storedPathsFromLibrary[$decodedPath]);
    }

    public function imageUrl(?string $path): ?string
    {
        return MediaUrl::resolve($path);
    }

    private function deleteStoredPath(string $path): void
    {
        // Never physically delete an asset known to be shared via the
        // library picker — it may still be in use on another record.
        $isSharedAsset = SharedMediaAsset::query()->where('public_id', $path)->exists();

        // If it's a Cloudinary public_id, delete from Cloudinary
        if (! $isSharedAsset && $this->isCloudinary && CloudinaryUrl::isCloudinaryResource($path)) {
            try {
                $cloudinaryService = app(CloudinaryUploadService::class);
                $cloudinaryService->delete($path);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        Storage::disk('public')->delete($path);
        LivewireSecureUploads::forget(request(), $path);
    }

    /**
     * @return array<int, string>
     */
    private function fileRules(): array
    {
        $rules = [
            'file',
            'image',
            'max:'.$this->maxSizeKb,
            'mimes:jpg,jpeg,png,webp',
            'mimetypes:image/jpeg,image/png,image/webp',
        ];

        $dimensions = [];

        if ($this->minWidth !== null) {
            $dimensions[] = 'min_width='.$this->minWidth;
        }

        if ($this->minHeight !== null) {
            $dimensions[] = 'min_height='.$this->minHeight;
        }

        if ($dimensions !== []) {
            $rules[] = 'dimensions:'.implode(',', $dimensions);
        }

        return $rules;
    }

    public function render()
    {
        return view('livewire.uploads.secure-image-upload');
    }
}
