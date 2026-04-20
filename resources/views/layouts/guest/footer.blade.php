<footer class="bg-slate-950 text-white">

    {{-- Main footer grid --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid lg:grid-cols-[1.4fr_2fr] gap-12">

            {{-- Brand column --}}
            <div>
                <img src="{{ asset('logo-dark.svg') }}" class="h-10 w-auto rounded-md p-1 mb-5" alt="Printbuka" />
                <p class="text-sm text-slate-400 leading-7 max-w-xs mb-6">
                    Nigeria's online print shop for businesses, events and individuals. Quality prints, branded gifts and fast nationwide delivery.
                </p>
                <div class="space-y-2 text-sm font-semibold text-slate-300">
                    <p>📞 {{ $siteSettings['contact_phone'] ?? '08035245784, 09054784526' }}</p>
                    <p>✉️ {{ $siteSettings['contact_email'] ?? 'sales@printbuka.com.ng' }}</p>
                </div>

                {{-- Stats strip --}}
                <div class="grid grid-cols-3 gap-3 mt-8">
                    <div class="bg-white/5 rounded-xl p-3 text-center border border-white/10">
                        <p class="text-xl font-black text-white">15k+</p>
                        <p class="text-xs text-slate-400 mt-0.5">Orders</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3 text-center border border-white/10">
                        <p class="text-xl font-black text-white">36</p>
                        <p class="text-xs text-slate-400 mt-0.5">States</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3 text-center border border-white/10">
                        <p class="text-xl font-black text-white">24h</p>
                        <p class="text-xs text-slate-400 mt-0.5">Review</p>
                    </div>
                </div>
            </div>

            {{-- Links grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-8">
                <div>
                    <h3 class="footer-title text-cyan-300 opacity-100 mb-4 text-xs font-black uppercase tracking-wide">Products</h3>
                    <nav class="flex flex-col gap-3 text-sm text-slate-400">
                        <a href="{{ route('products.index') }}#catalog" class="hover:text-white transition">Business Cards</a>
                        <a href="{{ route('products.index') }}#catalog" class="hover:text-white transition">Flyers & Posters</a>
                        <a href="{{ route('products.index') }}#catalog" class="hover:text-white transition">Brochures</a>
                        <a href="{{ route('products.index') }}#catalog" class="hover:text-white transition">Stickers & Labels</a>
                        <a href="{{ route('products.index') }}#catalog" class="hover:text-white transition">Letterheads</a>
                    </nav>
                </div>

                <div>
                    <h3 class="footer-title text-cyan-300 opacity-100 mb-4 text-xs font-black uppercase tracking-wide">Gifts & Services</h3>
                    <nav class="flex flex-col gap-3 text-sm text-slate-400">
                        <a href="{{ route('categories.index') }}" class="hover:text-white transition">Branded Mugs</a>
                        <a href="{{ route('categories.index') }}" class="hover:text-white transition">T-shirts & Hoodies</a>
                        <a href="{{ route('categories.index') }}" class="hover:text-white transition">Tote Bags</a>
                        <a href="{{ route('products.index') }}#uv-dtf-products" class="hover:text-white transition">UV-DTF Transfers</a>
                        <a href="{{ route('products.index') }}#laser-engraving-products" class="hover:text-white transition">Laser Engraving</a>
                    </nav>
                </div>

                <div>
                    <h3 class="footer-title text-cyan-300 opacity-100 mb-4 text-xs font-black uppercase tracking-wide">Company</h3>
                    <nav class="flex flex-col gap-3 text-sm text-slate-400">
                        <a href="#" class="hover:text-white transition">About Printbuka</a>
                        <a href="{{ route('partners.create') }}" class="hover:text-white transition">Become a Partner</a>
                        <a href="{{ route('blog') }}" class="hover:text-white transition">Blog</a>
                        <a href="#" class="hover:text-white transition">Contact Us</a>
                    </nav>
                </div>

                <div>
                    <h3 class="footer-title text-cyan-300 opacity-100 mb-4 text-xs font-black uppercase tracking-wide">Support</h3>
                    <nav class="flex flex-col gap-3 text-sm text-slate-400">
                        <a href="{{ route('orders.track') }}" class="hover:text-white transition">Track Order</a>
                        <a href="{{ route('quotes.create') }}" class="hover:text-white transition">Get a Quote</a>
                        <a href="{{ route('services.index') }}" class="hover:text-white transition">Services</a>
                        <a href="#" class="hover:text-white transition">Artwork Guide</a>
                        <a href="{{ route('policies.terms') }}" class="hover:text-white transition">Terms</a>
                        <a href="{{ route('policies.privacy') }}" class="hover:text-white transition">Privacy</a>
                        <a href="{{ route('policies.refund') }}" class="hover:text-white transition">Refunds</a>
                    </nav>
                </div>
            </div>

        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="border-t border-white/10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-slate-500">
                <p>&copy; {{ date('Y') }} {{ $siteSettings['site_name'] ?? 'Printbuka Limited' }}. All rights reserved.</p>
                <div class="flex gap-5">
                    <a href="{{ route('policies.privacy') }}" class="hover:text-white transition">Privacy</a>
                    <a href="{{ route('policies.terms') }}" class="hover:text-white transition">Terms</a>
                    <a href="{{ route('policies.refund') }}" class="hover:text-white transition">Refunds</a>
                </div>
                <p>
                    Built with ❤️ by
                    <a href="https://www.aidigitalagency.com.ng" target="_blank" rel="noopener noreferrer" class="font-bold text-white hover:text-cyan-300 transition">AI Digital Agency</a>
                </p>
            </div>
        </div>
    </div>

</footer>