@extends('layouts.admin')

@section('title', 'Product Management | Printbuka')

@section('content')
    <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-pink-700">Product Management</p>
                    <h1 class="mt-2 text-4xl text-slate-950">Products.</h1>
                </div>
                <a href="{{ route('admin.products.create') }}" class="rounded-md bg-pink-600 px-5 py-3 text-sm font-black text-white transition hover:bg-pink-700">Create Product</a>
            </div>
            @if (session('status'))<p class="mt-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('status') }}</p>@endif
            <div class="mt-8 overflow-x-auto rounded-md border border-slate-200 bg-white shadow-sm">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead><tr class="border-b border-slate-200 bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500"><th class="px-5 py-4">Name</th><th class="px-5 py-4">Category</th><th class="px-5 py-4">MOQ</th><th class="px-5 py-4">Price</th><th class="px-5 py-4">Status</th><th class="px-5 py-4"></th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-5 py-4 font-black">{{ $product->name }}</td>
                                <td class="px-5 py-4">{{ $product->category?->name ?? 'Unassigned' }}</td>
                                <td class="px-5 py-4">{{ $product->moq }}</td>
                                <td class="px-5 py-4">NGN {{ number_format((float) $product->price, 2) }}</td>
                                <td class="px-5 py-4">{{ $product->is_active ? 'Active' : 'Hidden' }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="font-black text-pink-700 hover:text-pink-800">Edit</a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="inline">@csrf @method('DELETE') <button class="ml-4 font-black text-slate-500 hover:text-red-700">Delete</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No products yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $products->links() }}</div>
    </div>
@endsection
