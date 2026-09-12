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

// ─── Web Push: show an OS-level notification for any push event, even with
// no admin tab open, and route a click to the relevant admin page. ───
self.addEventListener('push', (event) => {
    if (!event.data) return;

    let payload = {};
    try {
        payload = event.data.json();
    } catch {
        return;
    }

    const title = payload.title || 'Printbuka';
    const data = payload.data || {};

    event.waitUntil(
        self.registration.showNotification(title, {
            body: payload.body || '',
            icon: payload.icon || '/android-icon-192x192.png',
            badge: payload.badge,
            tag: payload.tag,
            data,
            // Explicit, not just the default: these are mandatory staff
            // notifications, so always play the OS/browser's notification
            // sound and vibrate on devices that support it — never silent.
            silent: false,
            vibrate: [200, 100, 200],
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.action_url || '/admin/dashboard';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url === targetUrl && 'focus' in client) {
                    return client.focus();
                }
            }
            if (self.clients.openWindow) {
                return self.clients.openWindow(targetUrl);
            }
        })
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
