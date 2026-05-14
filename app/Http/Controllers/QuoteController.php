<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\ExternalAssetLinks;
use App\Support\JobAssetUpload;
use App\Support\ReferenceCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QuoteController extends Controller
{
    public function create(Request $request): View
    {
        $selectedProduct = null;

        if ($request->filled('product_id')) {
            $selectedProduct = Product::query()
                ->where('is_active', true)
                ->find($request->integer('product_id'));
        }

        return view('quotes.create', [
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
            'selectedProduct' => $selectedProduct,
            'categories' => ProductCategory::publicTreeQuery()
                ->limit(6)
                ->get(),
            'jobTypes' => config('printbuka_admin.job_types'),
            'sizes' => config('printbuka_admin.sizes'),
            'materials' => config('printbuka_admin.materials'),
            'finishes' => config('printbuka_admin.finishes'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['nullable', 'exists:products,id'],
            'job_type' => ['required', 'string', 'max:255'],
            'size_format' => ['nullable', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'quote_budget' => ['nullable', 'numeric', 'min:0'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'delivery_city' => ['nullable', 'string', 'max:255'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'material_substrate' => ['nullable', 'string', 'max:255'],
            'finish_lamination' => ['nullable', 'string', 'max:255'],
            'artwork_notes' => ['nullable', 'string', 'max:2000'],
            'asset_drive_links' => ['nullable', 'string', 'max:4000'],
            'job_asset_image_paths' => ['nullable', 'array'],
            'job_asset_image_paths.*' => ['string', 'max:255'],
        ]);

        if ($request->hasFile('job_asset_files')) {
            throw ValidationException::withMessages([
                'asset_drive_links' => 'Document or ZIP uploads are blocked. Please share a Google Drive, OneDrive, or MediaFire link instead.',
            ]);
        }

        $invalidLinks = ExternalAssetLinks::invalidLinks($validated['asset_drive_links'] ?? null);

        if ($invalidLinks !== []) {
            throw ValidationException::withMessages([
                'asset_drive_links' => 'Use valid external links from Google Drive, OneDrive, MediaFire, Dropbox, WeTransfer, or Mega only.',
            ]);
        }

        $validated['artwork_notes'] = ExternalAssetLinks::appendToNotes(
            $validated['artwork_notes'] ?? null,
            $validated['asset_drive_links'] ?? null
        );

        unset($validated['asset_drive_links'], $validated['job_asset_image_paths']);

        $order = Order::query()->create([
            ...$validated,
            'user_id' => Auth::id(),
            'service_type' => 'quote',
            'job_order_number' => ReferenceCode::jobOrderNumber('quote'),
            'channel' => 'Online',
            'unit_price' => 0,
            'total_price' => 0,
            'priority' => '🟡 Normal',
            'status' => 'Quote Requested',
            'payment_status' => 'Awaiting Invoice',
            'job_image_assets' => JobAssetUpload::fromRequest($request),
        ]);

        session()->put('tracked_orders.'.$order->id, true);

        return redirect()
            ->route('quotes.success', $order)
            ->with('status', 'Your quote request has been received. Our team will review it and follow up.');
    }

    public function success(Order $order): View
    {
        return view('quotes.success', [
            'order' => $order->load('product'),
        ]);
    }
}
