// Service worker for the Printbuka Staff PWA / Capacitor shell.
//
// Deliberately conservative: this app is server-rendered Blade + Livewire,
// not a SPA, so caching HTML would risk serving a stale CSRF token or stale
// page state. Only versioned, hashed static assets (Vite build output,
// icons) are cache-first — everything else (HTML, Livewire's own requests,
// API calls) goes straight to the network untouched.

const CACHE_NAME = 'pb-staff-static-v1';
const CACHEABLE_PATH_PATTERNS = [/^\/build\/assets\//, /^\/(android|apple)-icon-/, /^\/favicon/];

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => Promise.all(
            keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
        )).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    if (event.request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    const isCacheable = CACHEABLE_PATH_PATTERNS.some((pattern) => pattern.test(url.pathname));
    if (!isCacheable) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => {
            if (cached) return cached;

            return fetch(event.request).then((response) => {
                if (response.ok) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                }
                return response;
            });
        })
    );
});
