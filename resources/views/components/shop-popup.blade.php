@php
    $popupData = json_encode($popupShopProducts ?? []);
@endphp

<div
    x-data="shopPopup({{ $popupData }})"
    x-init="init()"
    x-show="open"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[110] flex items-end sm:items-center justify-center p-4 sm:p-6"
    style="display:none;"
    @keydown.escape.window="dismiss()"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" @click="dismiss()"></div>

    {{-- Card --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm sm:max-w-md overflow-hidden z-10"
    >
        {{-- Close button --}}
        <button @click="dismiss()" class="absolute top-3 right-3 z-10 btn btn-xs btn-ghost btn-circle text-slate-400 hover:text-slate-700 bg-white/80">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Product image --}}
        <div class="relative h-52 overflow-hidden bg-slate-100">
            <img :src="product.image" :alt="product.name"
                 class="w-full h-full object-cover"
                 onerror="this.onerror=null;this.src='/img/product-placeholder.svg';" />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/50 to-transparent"></div>

            {{-- Urgency badge --}}
            <div class="absolute top-3 left-3 flex gap-2">
                <span class="badge badge-sm bg-pink-600 border-0 text-white font-black animate-pulse">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                    </svg>
                    Trending now
                </span>
            </div>

            <div class="absolute bottom-3 left-4">
                <p class="text-xs text-white/70 font-bold">People are viewing this right now</p>
            </div>
        </div>

        {{-- Content --}}
        <div class="p-5">
            <p class="text-xs font-black uppercase tracking-wide text-pink-600 mb-1">Limited Stock Available</p>
            <h3 class="text-lg font-black text-slate-950 leading-snug mb-1" x-text="product.name"></h3>
            <p class="text-sm text-slate-500 line-clamp-2 mb-4" x-text="product.short_description"></p>

            {{-- Pricing --}}
            <div class="flex items-center gap-3 mb-5">
                <span class="text-2xl font-black text-pink-600" x-text="'NGN ' + product.current_price"></span>
                <template x-if="product.is_on_sale">
                    <span class="text-sm font-bold text-slate-400 line-through" x-text="'NGN ' + product.price"></span>
                </template>
                <template x-if="product.is_on_sale">
                    <span class="badge badge-sm bg-emerald-100 text-emerald-700 border-0 font-black">Sale!</span>
                </template>
            </div>

            {{-- CTAs --}}
            <div class="flex flex-col gap-2">
                <a :href="product.url"
                   @click="localStorage.setItem('pb_popup_last', Date.now().toString())"
                   class="btn bg-pink-600 border-0 text-white hover:bg-pink-700 font-black w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Shop Now — Don't Miss Out
                </a>
                <button @click="snooze()" class="btn btn-ghost btn-sm font-bold text-slate-400 hover:text-slate-600">
                    Maybe later
                </button>
            </div>

            <p class="text-xs text-center text-slate-400 mt-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 inline-block mr-1 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                Secure checkout via Paystack
            </p>
        </div>
    </div>
</div>

<script>
function shopPopup(products) {
    return {
        open: false,
        product: null,
        _timer: null,

        init() {
            if (!products || !products.length) return;

            const key = 'pb_popup_last';
            const last = localStorage.getItem(key);
            const cooldown = 24 * 60 * 60 * 1000; // 24 hours

            if (last && (Date.now() - parseInt(last, 10)) < cooldown) return;

            this.product = products[Math.floor(Math.random() * products.length)];

            // Show after 10 s, or after user scrolls 40% of the page
            this._timer = setTimeout(() => { this.open = true; }, 10000);

            const onScroll = () => {
                const scrolled = window.scrollY / (document.body.scrollHeight - window.innerHeight);
                if (scrolled >= 0.4) {
                    clearTimeout(this._timer);
                    this.open = true;
                    window.removeEventListener('scroll', onScroll);
                }
            };
            window.addEventListener('scroll', onScroll, { passive: true });
        },

        dismiss() {
            this.open = false;
            localStorage.setItem('pb_popup_last', Date.now().toString());
            clearTimeout(this._timer);
        },

        snooze() {
            this.open = false;
            // Only snooze 2 hours so they may see it again sooner
            localStorage.setItem('pb_popup_last', (Date.now() - 22 * 60 * 60 * 1000).toString());
            clearTimeout(this._timer);
        },
    };
}
</script>
