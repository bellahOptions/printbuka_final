<?php

namespace App\Livewire\Uploads;

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

        if (filled($this->storedPath)) {
            $this->deleteStoredPath((string) $this->storedPath);
        }

        $path = $this->upload->store($this->directory, 'public');
        $this->storedPath = $path;
        LivewireSecureUploads::register(request(), $path);

        $this->upload = null;
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

        foreach ($this->uploads as $upload) {
            if (! $upload) {
                continue;
            }

            if (count($this->storedPaths) >= $this->maxFiles) {
                $this->addError('uploads', "You can upload up to {$this->maxFiles} images.");
                break;
            }

            $path = $upload->store($this->directory, 'public');
            $this->storedPaths[] = $path;
            LivewireSecureUploads::register(request(), $path);
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

        $this->deleteStoredPath((string) $this->storedPath);
        $this->storedPath = null;
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

        $this->deleteStoredPath($decodedPath);
    }

    public function imageUrl(?string $path): ?string
    {
        return MediaUrl::resolve($path);
    }

    private function deleteStoredPath(string $path): void
    {
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
