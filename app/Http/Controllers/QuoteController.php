<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Support\JobAssetUpload;
use App\Support\ReferenceCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuoteController extends Controller
{
    public function create(): View
    {
        return view('quotes.create', [
            'products' => Product::query()->where('is_active', true)->orderBy('name')->get(),
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
            'job_asset_files' => ['nullable', 'array'],
            'job_asset_files.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf,svg,zip', 'max:20480'],
        ]);

        unset($validated['job_asset_files']);

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
