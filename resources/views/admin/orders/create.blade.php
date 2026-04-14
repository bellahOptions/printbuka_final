@extends('layouts.admin')

@section('title', 'Create Admin Job | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
            <div class="rounded-md bg-slate-950 p-6 text-white lg:p-8">
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-black text-cyan-300 hover:text-cyan-200">Back to Job Tracker</a>
                <h1 class="mt-3 text-4xl">Create a new job.</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">Log the client brief, create the job order, and send the invoice in one step.</p>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-800">
                    <p>Check the highlighted details and try again.</p>
                </div>
            @endif

            <form action="{{ route('admin.orders.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
                @csrf

                <section class="space-y-6">
                    <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Client</p>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <label class="text-sm font-black">Client Name<input name="customer_name" value="{{ old('customer_name') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                            <label class="text-sm font-black">Client Email<input type="email" name="customer_email" value="{{ old('customer_email') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                            <label class="text-sm font-black">Client Phone<input name="customer_phone" value="{{ old('customer_phone') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                            <label class="text-sm font-black">Delivery City<input name="delivery_city" value="{{ old('delivery_city') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                            <label class="text-sm font-black sm:col-span-2">Delivery Address<input name="delivery_address" value="{{ old('delivery_address') }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                        </div>
                    </div>

                    <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Job Brief</p>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <label class="text-sm font-black">Channel<select name="channel" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">@foreach ($channels as $channel)<option @selected(old('channel', 'Manual') === $channel)>{{ $channel }}</option>@endforeach</select></label>
                            <label class="text-sm font-black">Product<select name="product_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Custom job</option>@foreach ($products as $product)<option value="{{ $product->id }}" @selected((int) old('product_id') === $product->id)>{{ $product->name }}</option>@endforeach</select></label>
                            <label class="text-sm font-black">Job Type<select name="job_type" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select job type</option>@foreach ($jobTypes as $jobType)<option @selected(old('job_type') === $jobType)>{{ $jobType }}</option>@endforeach</select></label>
                            <label class="text-sm font-black">Size / Format<select name="size_format" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select size</option>@foreach ($sizes as $size)<option @selected(old('size_format') === $size)>{{ $size }}</option>@endforeach</select></label>
                            <label class="text-sm font-black">Priority<select name="priority" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">@foreach ($priorities as $priority)<option @selected(old('priority', '🟡 Normal') === $priority)>{{ $priority }}</option>@endforeach</select></label>
                            <label class="text-sm font-black">Assigned Designer<select name="assigned_designer_id" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select designer</option>@foreach ($staff as $person)<option value="{{ $person->id }}" @selected((int) old('assigned_designer_id') === $person->id)>{{ $person->displayName() }} · {{ $person->department }}</option>@endforeach</select></label>
                            <label class="text-sm font-black">Brief Date<input type="datetime-local" name="brief_received_at" value="{{ old('brief_received_at', now()->format('Y-m-d\\TH:i')) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                            <label class="text-sm font-black">Material / Substrate<select name="material_substrate" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select material</option>@foreach ($materials as $material)<option @selected(old('material_substrate') === $material)>{{ $material }}</option>@endforeach</select></label>
                            <label class="text-sm font-black">Finish / Lamination<select name="finish_lamination" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"><option value="">Select finish</option>@foreach ($finishes as $finish)<option @selected(old('finish_lamination') === $finish)>{{ $finish }}</option>@endforeach</select></label>
                            <label class="text-sm font-black sm:col-span-2">Artwork Notes<textarea name="artwork_notes" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('artwork_notes') }}</textarea></label>
                            <label class="text-sm font-black sm:col-span-2">Job Image / Artwork Assets<input type="file" name="job_asset_files[]" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.svg,.zip" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold"><span class="mt-2 block text-xs font-bold text-slate-500">Upload client artwork, images, PDFs, SVG files or ZIP archives up to 20MB each.</span></label>
                        </div>
                    </div>

                    <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-emerald-700">Invoice</p>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <label class="text-sm font-black">Quantity<input type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                            <label class="text-sm font-black">Unit Price (NGN)<input type="number" min="0" step="0.01" name="unit_price" value="{{ old('unit_price') }}" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                            <label class="text-sm font-black">Amount Paid (NGN)<input type="number" min="0" step="0.01" name="amount_paid" value="{{ old('amount_paid', 0) }}" class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold"></label>
                            <label class="text-sm font-black">Payment Status<select name="payment_status" required class="mt-2 min-h-12 w-full rounded-md border border-slate-200 px-4 font-semibold">@foreach ($paymentStatuses as $status)<option @selected(old('payment_status', 'Invoice Issued') === $status)>{{ $status }}</option>@endforeach</select></label>
                            <label class="text-sm font-black sm:col-span-2">Internal Notes<textarea name="internal_notes" rows="4" class="mt-2 w-full rounded-md border border-slate-200 px-4 py-3 font-semibold">{{ old('internal_notes') }}</textarea></label>
                        </div>
                    </div>

                    <button class="w-full rounded-md bg-pink-600 px-5 py-4 text-sm font-black text-white transition hover:bg-pink-700">Create Job & Send Invoice</button>
                </section>

                <aside class="space-y-6">
                    <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-pink-700">Access</p>
                        <p class="mt-3 text-sm leading-6 text-slate-600">Only Super Admin, Management, and Customer Service can create jobs. The job starts at Analyzing Job Brief and is assigned a PB-YYYY-XXXX number after saving.</p>
                    </div>

                    <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-sm font-black uppercase tracking-wide text-cyan-700">Invoice Email</p>
                        <p class="mt-3 text-sm leading-6 text-slate-600">An invoice is generated immediately and sent to the client email address on this form.</p>
                    </div>
                </aside>
            </form>
    </div>
@endsection
