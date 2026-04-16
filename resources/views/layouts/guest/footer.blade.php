<footer class="border-t border-slate-200 bg-slate-950 text-white">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-[1.2fr_2fr] lg:px-8">
        <div>
            <img src="{{ asset('logo-dark.svg') }}" class="h-10 w-auto rounded-md p-1" alt="Printbuka Logo" />
            <p class="mt-5 max-w-sm text-sm leading-7 text-slate-300">Custom printing, branding and corporate gifts for teams, events, campaigns and everyday business needs.</p>
            <div class="mt-6 space-y-2 text-sm font-semibold text-slate-200">
                <p>{{ $siteSettings['contact_phone'] ?? '08035245784, 09054784526' }}</p>
                <p>{{ $siteSettings['contact_email'] ?? 'sales@printbuka.com.ng' }}</p>
            </div>
        </div>

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <h2 class="text-sm font-black uppercase tracking-wide text-cyan-300">Products</h2>
                <div class="mt-4 space-y-3 text-sm text-slate-300">
                    <a href="{{ route('products.index') }}#catalog" class="block transition hover:text-white">Business Cards</a>
                    <a href="{{ route('products.index') }}#catalog" class="block transition hover:text-white">Flyers and Posters</a>
                    <a href="{{ route('products.index') }}#catalog" class="block transition hover:text-white">Brochures</a>
                    <a href="{{ route('products.index') }}#catalog" class="block transition hover:text-white">Stickers and Labels</a>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-black uppercase tracking-wide text-cyan-300">Gifts</h2>
                <div class="mt-4 space-y-3 text-sm text-slate-300">
                    <a href="{{ route('categories.index') }}" class="block transition hover:text-white">Branded Mugs</a>
                    <a href="{{ route('categories.index') }}" class="block transition hover:text-white">T-shirts</a>
                    <a href="{{ route('categories.index') }}" class="block transition hover:text-white">Tote Bags</a>
                    <a href="{{ route('categories.index') }}" class="block transition hover:text-white">Corporate Gift Sets</a>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-black uppercase tracking-wide text-cyan-300">Company</h2>
                <div class="mt-4 space-y-3 text-sm text-slate-300">
                    <a href="#" class="block transition hover:text-white">About Printbuka</a>
                    <a href="{{ route('partners.create') }}" class="block transition hover:text-white">Become a Partner</a>
                    <a href="#" class="block transition hover:text-white">Contact</a>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-black uppercase tracking-wide text-cyan-300">Support</h2>
                <div class="mt-4 space-y-3 text-sm text-slate-300">
                    <a href="{{ route('orders.track') }}" class="block transition hover:text-white">Track Order</a>
                    <a href="{{ route('quotes.create') }}" class="block transition hover:text-white">Get Quote</a>
                    <a href="{{ route('services.index') }}" class="block transition hover:text-white">Services</a>
                    <a href="{{ route('policies.terms') }}" class="block transition hover:text-white">Terms & Conditions</a>
                    <a href="{{ route('policies.privacy') }}" class="block transition hover:text-white">Privacy Policy</a>
                    <a href="{{ route('policies.refund') }}" class="block transition hover:text-white">Refund Policy</a>
                    <a href="#" class="block transition hover:text-white">Artwork Guide</a>
                    <a href="#" class="block transition hover:text-white">Delivery</a>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-6 text-sm text-slate-400 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>&copy; {{ date('Y') }} {{ $siteSettings['site_name'] ?? 'Printbuka' }}. All rights reserved.</p>
            <p>
                Built with Love by
                <a href="https://www.aidigitalagency.com.ng" target="_blank" rel="noopener noreferrer" class="font-bold text-white transition hover:text-cyan-300">AI Digital Agency</a>
            </p>
            <div class="flex gap-5">
                <a href="{{ route('policies.privacy') }}" class="transition hover:text-white">Privacy</a>
                <a href="{{ route('policies.terms') }}" class="transition hover:text-white">Terms</a>
                <a href="{{ route('policies.refund') }}" class="transition hover:text-white">Refunds</a>
            </div>
        </div>
    </div>
</footer>
