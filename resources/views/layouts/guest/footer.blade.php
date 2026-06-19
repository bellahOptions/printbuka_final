@php
    $phone   = $siteSettings['contact_phone']   ?? '08035245784, 09054784526';
    $email   = $siteSettings['contact_email']   ?? 'sales@printbuka.com.ng';
    $address = $siteSettings['contact_address'] ?? 'Lagos, Nigeria';
    $hours   = $siteSettings['business_hours']  ?? 'Mon – Sat: 9 am – 6 pm';
    $siteName = $siteSettings['site_name']      ?? 'Printbuka';
@endphp

<footer class="relative bg-slate-950 text-white overflow-hidden">

    {{-- Subtle grid texture overlay --}}
    <div class="pointer-events-none absolute inset-0 opacity-[0.025]"
         style="background-image: repeating-linear-gradient(0deg,#fff 0,#fff 1px,transparent 1px,transparent 60px),repeating-linear-gradient(90deg,#fff 0,#fff 1px,transparent 1px,transparent 60px);">
    </div>
    {{-- Glow blobs --}}
    <div class="pointer-events-none absolute -top-40 -left-40 w-[500px] h-[500px] rounded-full bg-pink-700/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-40 -right-40 w-[400px] h-[400px] rounded-full bg-violet-700/8 blur-3xl"></div>

    {{-- ===== MAIN WIDGETS ===== --}}
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-16 pb-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-8">

            {{-- ── Column 1: Brand + Contact ─────────────────────── --}}
            <div>
                <div class="mb-6">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('logo-dark.svg') }}"
                             class="h-10 w-auto"
                             alt="{{ $siteName }}"
                             onerror="this.onerror=null;this.src='{{ asset('logo.png') }}';" />
                    </a>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-pink-600/20 border border-pink-600/30 flex items-center justify-center shrink-0 mt-0.5">
                            <x-heroicon-o-map-pin class="w-4 h-4 text-pink-400" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-0.5">Address</p>
                            <p class="text-sm font-semibold text-slate-200 leading-snug">{{ $address }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-pink-600/20 border border-pink-600/30 flex items-center justify-center shrink-0 mt-0.5">
                            <x-heroicon-o-phone class="w-4 h-4 text-pink-400" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-0.5">Phone Number</p>
                            <a href="tel:{{ preg_replace('/\s+/', '', explode(',', $phone)[0]) }}"
                               class="text-sm font-semibold text-slate-200 hover:text-pink-400 transition-colors leading-snug">
                                {{ $phone }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-9 h-9 rounded-lg bg-pink-600/20 border border-pink-600/30 flex items-center justify-center shrink-0 mt-0.5">
                            <x-heroicon-o-envelope class="w-4 h-4 text-pink-400" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-0.5">Email</p>
                            <a href="mailto:{{ $email }}"
                               class="text-sm font-semibold text-slate-200 hover:text-pink-400 transition-colors">
                                {{ $email }}
                            </a>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-white/10">
                        <p class="text-xs font-black text-slate-400 uppercase tracking-wide mb-1">Open Hours</p>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            {{ $hours }}<br>
                            <span class="text-slate-500">Sunday: CLOSED</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- ── Column 2: Useful Links ──────────────────────────── --}}
            <div>
                <div class="mb-6">
                    <h3 class="text-base font-black text-white relative inline-block pb-3 after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-8 after:h-0.5 after:bg-pink-600 after:rounded-full">
                        Useful Links
                    </h3>
                </div>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('home') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            All Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services.index') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            How It Works
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('orders.track') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            Track Order
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('blog') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            Blog
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('policies.terms') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            Terms &amp; Services
                        </a>
                    </li>
                </ul>
            </div>

            {{-- ── Column 3: More Services ─────────────────────────── --}}
            <div>
                <div class="mb-6">
                    <h3 class="text-base font-black text-white relative inline-block pb-3 after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-8 after:h-0.5 after:bg-pink-600 after:rounded-full">
                        More Services
                    </h3>
                </div>
                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('products.index') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            Digital Printing
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services.index') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            Offset Printing
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services.show', 'uv-dtf') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            UV-DTF Transfer
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services.index') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            DTF Printing
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('services.show', 'laser-engraving') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            Laser Engraving
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('products.index') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            T-Shirt Printing
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shop.index') }}"
                           class="flex items-center gap-2 text-sm text-slate-400 hover:text-pink-400 hover:translate-x-1 transition-all group">
                            <x-heroicon-o-chevron-right class="w-3.5 h-3.5 text-pink-600/60 group-hover:text-pink-400 shrink-0" />
                            Branded Gifts Shop
                        </a>
                    </li>
                </ul>
            </div>

            {{-- ── Column 4: Newsletter + Social ───────────────────── --}}
            <div>
                <div class="mb-6">
                    <h3 class="text-base font-black text-white relative inline-block pb-3 after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-8 after:h-0.5 after:bg-pink-600 after:rounded-full">
                        Newsletter
                    </h3>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed mb-5">
                    Subscribe for print tips, offers and updates from our team.
                </p>

                {{-- Newsletter form --}}
                <form action="#" method="POST" class="mb-7" onsubmit="return false;">
                    <div class="flex rounded-xl overflow-hidden border border-white/10 bg-white/5 focus-within:border-pink-600/60 transition-colors">
                        <input type="email"
                               placeholder="Your email address"
                               class="flex-1 bg-transparent text-sm text-white placeholder-slate-500 px-4 py-3 outline-none min-w-0" />
                        <button type="submit"
                                class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-3 transition-colors shrink-0 flex items-center justify-center"
                                aria-label="Subscribe">
                            <x-heroicon-o-envelope class="w-4 h-4" />
                        </button>
                    </div>
                </form>

                {{-- Social icons --}}
                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-3">Follow Us</p>
                    <div class="flex items-center gap-2">
                        <a href="{{ $siteSettings['social_facebook'] ?? '#' }}"
                           target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 hover:bg-pink-600 hover:border-pink-600 flex items-center justify-center transition-all"
                           aria-label="Facebook">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="{{ $siteSettings['social_twitter'] ?? '#' }}"
                           target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 hover:bg-pink-600 hover:border-pink-600 flex items-center justify-center transition-all"
                           aria-label="X / Twitter">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="{{ $siteSettings['social_instagram'] ?? '#' }}"
                           target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 hover:bg-pink-600 hover:border-pink-600 flex items-center justify-center transition-all"
                           aria-label="Instagram">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="{{ $siteSettings['social_whatsapp'] ?? '#' }}"
                           target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-lg bg-white/5 border border-white/10 hover:bg-pink-600 hover:border-pink-600 flex items-center justify-center transition-all"
                           aria-label="WhatsApp">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== BOTTOM BAR ===== --}}
    <div class="relative border-t border-white/10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-slate-500">
                <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
                <div class="flex items-center gap-1 text-xs text-slate-600">
                    {{-- Payment icons strip --}}
                    <span class="px-2 py-1 rounded bg-white/5 border border-white/10 font-bold text-slate-400 text-[10px] tracking-wide">VISA</span>
                    <span class="px-2 py-1 rounded bg-white/5 border border-white/10 font-bold text-slate-400 text-[10px] tracking-wide">MASTERCARD</span>
                    <span class="px-2 py-1 rounded bg-white/5 border border-white/10 font-bold text-slate-400 text-[10px] tracking-wide">PAYSTACK</span>
                    <span class="px-2 py-1 rounded bg-white/5 border border-white/10 font-bold text-slate-400 text-[10px] tracking-wide">BANK</span>
                </div>
                <p>
                    Built with <x-heroicon-s-heart class="w-3.5 h-3.5 inline text-pink-500" /> by
                    <a href="https://www.aidigitalagency.com.ng" target="_blank" rel="noopener noreferrer"
                       class="font-bold text-white hover:text-pink-400 transition">AI Digital Agency</a>
                </p>
            </div>
        </div>
    </div>

</footer>
