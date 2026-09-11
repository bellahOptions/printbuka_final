<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Services\CloudinaryUploadService;
use App\Support\CloudinaryUrl;
use App\Support\ExecutiveAlert;
use App\Support\LivewireSecureUploads;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminVendorController extends Controller
{
    public function index(Request $request): View
    {
        $search     = trim((string) $request->input('search', ''));
        $vendorType = (string) $request->input('vendor_type', '');
        $category   = (string) $request->input('category', '');
        $status     = (string) $request->input('status', '');
        $sort       = (string) $request->input('sort', 'name');

        $query = Vendor::query();

        if ($search !== '') {
            $query->search($search);
        }

        if (in_array($vendorType, Vendor::VENDOR_TYPES, true)) {
            $query->where('vendor_type', $vendorType);
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        if (in_array($status, Vendor::STATUSES, true)) {
            $query->where('status', $status);
        }

        match ($sort) {
            'recent' => $query->latest(),
            'rating' => $query->orderByDesc('rating')->orderBy('name'),
            default => $query->orderBy('name'),
        };

        $vendors = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Vendor::count(),
            'active' => Vendor::active()->count(),
            'inactive' => Vendor::inactive()->count(),
            'blacklisted' => Vendor::blacklisted()->count(),
            'suppliers' => Vendor::ofType('Supplier')->count(),
            'engineers' => Vendor::ofType('Engineer')->count(),
        ];

        $categories = $this->categoryOptions();

        return view('admin.vendors.index', [
            'vendors' => $vendors,
            'stats' => $stats,
            'filters' => compact('search', 'vendorType', 'category', 'status', 'sort'),
            'vendorTypes' => Vendor::VENDOR_TYPES,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.vendors.form', [
            'vendor' => null,
            'vendorTypes' => Vendor::VENDOR_TYPES,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $vendor = Vendor::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        $logoUpdates = $this->syncLogo($request, $vendor);
        if ($logoUpdates !== []) {
            $vendor->update($logoUpdates);
        }

        ExecutiveAlert::send(
            title: 'New Vendor Added',
            body: $request->user()->displayName().' added "'.$vendor->displayName().'" to the vendor directory.',
            type: 'vendor_created',
            data: ['vendor_id' => $vendor->id, 'action_url' => route('admin.vendors.show', $vendor)],
            excludeUserId: $request->user()->id,
        );

        return redirect()->route('admin.vendors.index')->with('status', 'Vendor added.');
    }

    public function show(Vendor $vendor): View
    {
        return view('admin.vendors.show', [
            'vendor' => $vendor->load('createdBy'),
        ]);
    }

    public function edit(Vendor $vendor): View
    {
        return view('admin.vendors.form', [
            'vendor' => $vendor,
            'vendorTypes' => Vendor::VENDOR_TYPES,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(Request $request, Vendor $vendor): RedirectResponse
    {
        $validated = $this->validated($request, $vendor);

        $vendor->update($validated);

        $logoUpdates = $this->syncLogo($request, $vendor);
        if ($logoUpdates !== []) {
            $vendor->update($logoUpdates);
        }

        return redirect()->route('admin.vendors.index')->with('status', 'Vendor updated.');
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        $vendor->delete();

        return back()->with('status', 'Vendor deleted.');
    }

    /** @return \Illuminate\Support\Collection<int, string> */
    private function categoryOptions()
    {
        return Vendor::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Vendor $vendor = null): array
    {
        $id = $vendor?->id ?? 'NULL';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'vendor_type' => ['required', Rule::in(Vendor::VENDOR_TYPES)],
            'category' => ['nullable', 'string', 'max:100'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', "unique:vendors,email,{$id}"],
            'phone' => ['nullable', 'string', 'max:30'],
            'alternate_phone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'country' => ['nullable', 'string', 'max:120'],
            'tax_id' => ['nullable', 'string', 'max:60'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:40'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'status' => ['required', Rule::in(Vendor::STATUSES)],
            'tags' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['email'] = filled($data['email'] ?? '') ? $data['email'] : null;

        return $data;
    }

    /** @return array<string, mixed> */
    private function syncLogo(Request $request, Vendor $vendor): array
    {
        $updates = [];
        $cloudinary = app(CloudinaryUploadService::class);

        if ($request->boolean('remove_logo') && filled($vendor->logo)) {
            $this->deleteLogo((string) $vendor->logo);
            $updates['logo'] = null;
        }

        if ($request->hasFile('logo')) {
            if (filled($vendor->logo)) {
                $this->deleteLogo((string) $vendor->logo);
            }
            $result = $cloudinary->storeToBoth($request->file('logo'), 'vendors/logos', 'vendors/logos');
            $updates['logo'] = $result['cloudinary_public_id'] ?? $result['path'];
        } elseif (filled($request->input('logo_path'))) {
            $livewirePath = LivewireSecureUploads::consumePath(
                $request,
                (string) $request->input('logo_path'),
                ['vendors/logos']
            );

            if (! $livewirePath) {
                throw ValidationException::withMessages([
                    'logo' => 'The uploaded logo is invalid or expired. Please upload again.',
                ]);
            }

            if (filled($vendor->logo)) {
                $this->deleteLogo((string) $vendor->logo);
            }

            if (CloudinaryUrl::isCloudinaryResource($livewirePath)) {
                $updates['logo'] = $livewirePath;
            } elseif (CloudinaryUrl::isConfigured()) {
                $fullPath = Storage::disk('public')->path($livewirePath);
                $result = $cloudinary->upload($fullPath, ['folder' => 'vendors/logos']);
                $updates['logo'] = $result['public_id'] ?? $livewirePath;
            } else {
                $updates['logo'] = $livewirePath;
            }
        }

        return $updates;
    }

    private function deleteLogo(string $path): void
    {
        if ($path === '') {
            return;
        }

        if (CloudinaryUrl::isCloudinaryResource($path)) {
            try {
                app(CloudinaryUploadService::class)->delete($path);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        Storage::disk('public')->delete($path);
    }
}
