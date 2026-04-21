<div>
    @php
        $productImages = [
            'business' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
            'card' => 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?auto=format&fit=crop&w=900&q=80',
            'flyer' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
            'poster' => 'https://images.unsplash.com/photo-1598300042247-d088f8ab3a91?auto=format&fit=crop&w=900&q=80',
            'sticker' => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
            'label' => 'https://images.unsplash.com/photo-1605902711622-cfb43c44367f?auto=format&fit=crop&w=900&q=80',
            'brochure' => 'https://images.unsplash.com/photo-1586282391129-76a6df230234?auto=format&fit=crop&w=900&q=80',
            'menu' => 'https://images.unsplash.com/photo-1586282391129-76a6df230234?auto=format&fit=crop&w=900&q=80',
            'letterhead' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=900&q=80',
            'envelope' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=900&q=80',
            'mug' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
            'gift' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
            'shirt' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
            'tote' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?auto=format&fit=crop&w=900&q=80',
            'banner' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80',
            'event' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=900&q=80',
            'default' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?auto=format&fit=crop&w=900&q=80',
        ];
    @endphp

    <p class="text-sm font-bold text-slate-400 mb-6">
        {{ number_format($this->totalResults) }} {{ Str::plural('product', $this->totalResults) }} found
    </p>

    @if($products->isNotEmpty())
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($products as $product)
                @php
                    $pName = strtolower($product->name);
                    $uploadedImage = $product->featuredImageUrl();
                    $img = $uploadedImage ?: $productImages['default'];

                    if (! $uploadedImage) {
                        foreach ($productImages as $kw => $url) {
                            if ($kw !== 'default' && str_contains($pName, $kw)) {
                                $img = $url;
                                break;
                            }
                        }
                    }
                @endphp

                <article class="card bg-base-100 border border-slate-200 shadow-sm hover:-translate-y-1 hover:shadow-lg transition group">
                    <figure class="overflow-hidden h-48">
                        <a href="{{ route('products.show', $product) }}">
                            <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                        </a>
                    </figure>
                    <div class="card-body p-5">
                        @if($product->category)
                            <a href="{{ route('products.category', $product->category) }}" class="badge badge-sm bg-pink-100 text-pink-700 border-0 font-bold w-fit hover:bg-pink-200 transition">
                                {{ $product->category->name }}
                            </a>
                        @endif

                        <h3 class="font-black text-slate-950 text-base leading-snug mt-1">
                            <a href="{{ route('products.show', $product) }}" class="hover:text-pink-600 transition">{{ $product->name }}</a>
                        </h3>

                        <p class="text-sm text-slate-500 leading-relaxed min-h-[3rem]">{{ $product->short_description }}</p>

                        <div class="flex flex-wrap gap-1.5 mt-1">
                            @if($product->moq)
                                <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">MOQ {{ $product->moq }}</span>
                            @endif
                            @if($product->paper_size)
                                <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">{{ $product->paper_size }}</span>
                            @endif
                            @if($product->paper_density)
                                <span class="badge badge-sm bg-slate-100 border-0 text-slate-600 font-bold">{{ $product->paper_density }}</span>
                            @endif
                        </div>

                        <div class="mt-3">
                            <p class="text-xs font-bold text-slate-400">starting at</p>
                            <p class="text-xl font-black text-pink-600">
                                NGN {{ number_format($product->price, 0) }}
                                @if($product->moq)
                                    <span class="text-sm font-bold text-slate-400">/ {{ $product->moq }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="card-actions mt-4 grid grid-cols-2 gap-2">
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline font-black border-slate-200 hover:border-pink-400 hover:text-pink-700">View</a>
                            <a href="{{ route('orders.create', $product) }}" class="btn btn-sm btn-neutral font-black hover:bg-pink-700">Order</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if($this->hasMore)
            <div class="mt-10 flex flex-col items-center gap-3" wire:poll.visible="loadMore">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400" wire:loading.remove wire:target="loadMore">
                    Loading more products as you scroll...
                </span>
                <span class="text-xs font-bold uppercase tracking-wide text-pink-600" wire:loading wire:target="loadMore">
                    Loading more products...
                </span>
                <button type="button" wire:click="loadMore" class="btn btn-outline border-slate-300 hover:border-pink-400 hover:text-pink-700 font-black">
                    Load More
                </button>
            </div>
        @else
            <div class="mt-10 rounded-2xl border border-slate-200 bg-slate-50 px-6 py-4 text-center">
                <p class="text-sm font-black text-slate-700">You have reached the end of the catalog.</p>
            </div>
        @endif
    @else
        <div class="py-24 flex flex-col items-center text-center">
            <div class="relative w-40 h-40 mb-8">
                <div class="absolute inset-0 rounded-full border-4 border-dashed border-pink-200 animate-spin" style="animation-duration: 12s;"></div>
                <div class="absolute inset-4 rounded-full bg-pink-50 flex items-center justify-center border-2 border-pink-100">
                    <div class="text-center">
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-10 h-7 rounded-t-lg border-2 border-pink-300 bg-white flex items-end justify-center pb-1">
                                <div class="w-6 h-0.5 bg-pink-300 rounded"></div>
                            </div>
                            <div class="w-12 h-6 bg-pink-400 rounded-sm flex items-center justify-around px-1.5">
                                <div class="w-1.5 h-1.5 bg-pink-200 rounded-full"></div>
                                <div class="w-1.5 h-1.5 bg-pink-200 rounded-full"></div>
                                <div class="w-1.5 h-1.5 bg-pink-200 rounded-full"></div>
                            </div>
                            <div class="w-10 h-4 rounded-b border-2 border-pink-300 bg-white flex items-center justify-center">
                                <div class="w-5 h-0.5 bg-pink-200 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="text-3xl font-black text-slate-950 mb-3">No products match these filters yet.</h3>
            <p class="text-slate-500 max-w-md leading-relaxed mb-8">
                Try another category, keyword, or price range.
            </p>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('products.index') }}" class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black">Reset Filters</a>
                <a href="{{ route('categories.index') }}" class="btn btn-outline border-slate-300 hover:border-pink-400 hover:text-pink-700 font-black">Browse Categories</a>
            </div>
        </div>
    @endif
</div>
