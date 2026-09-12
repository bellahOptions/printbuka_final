// Browser Web Push for staff/admin pages viewed in a plain browser tab (the
// Capacitor-wrapped shell already gets native push via capacitor-bridge.js,
// so this deliberately no-ops there to avoid double-registering).
import { Capacitor } from '@capacitor/core';

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
const vapidKey = () => document.querySelector('meta[name="vapid-public-key"]')?.content || '';
const subscribeUrl = () => document.querySelector('meta[name="web-push-subscribe-url"]')?.content;
const unsubscribeUrl = () => document.querySelector('meta[name="web-push-unsubscribe-url"]')?.content;

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
}

// Returns true only on a genuine 2xx from the server — a failed registration
// here must NOT be swallowed, because the alternative is a browser that
// thinks it's subscribed (permission granted, live PushSubscription) while
// the backend never learns the endpoint exists, so nothing is ever sent and
// nothing ever looks wrong in the UI.
async function postJson(url, method, body) {
    try {
        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        });

        if (!response.ok) {
            console.error(`[webpush] ${method} ${url} failed with status ${response.status}`);

            return false;
        }

        return true;
    } catch (error) {
        console.error(`[webpush] ${method} ${url} failed:`, error);

        return false;
    }
}

async function enableWebPush(registration) {
    const key = vapidKey();
    if (!key) {
        console.error('[webpush] No VAPID public key found in the page — check VAPID_PUBLIC_KEY in .env.');

        return false;
    }

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        console.warn('[webpush] Notification permission was not granted:', permission);

        return false;
    }

    const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(key),
    });

    const url = subscribeUrl();
    if (!url) {
        console.error('[webpush] No subscribe URL found in the page meta tags.');
        await subscription.unsubscribe();

        return false;
    }

    const saved = await postJson(url, 'POST', subscription.toJSON());
    if (!saved) {
        // The browser is subscribed but the server doesn't know it — that's
        // worse than not subscribing at all (looks "on" but never fires),
        // so undo the browser-side subscription too.
        await subscription.unsubscribe();

        return false;
    }

    return true;
}

async function disableWebPush(registration) {
    const subscription = await registration.pushManager.getSubscription();
    if (!subscription) return;

    const url = unsubscribeUrl();
    if (url) {
        const removed = await postJson(url, 'DELETE', { endpoint: subscription.endpoint });
        if (!removed) {
            console.warn('[webpush] Server-side unsubscribe failed — unsubscribing this browser anyway.');
        }
    }

    await subscription.unsubscribe();
}

function updateToggleButtons(state) {
    document.querySelectorAll('[data-web-push-toggle]').forEach((btn) => {
        if (state === 'unsupported' || state === 'denied') {
            btn.hidden = true;
            return;
        }

        btn.hidden = false;
        btn.classList.toggle('text-brand-600', state === 'subscribed');
        btn.title = state === 'subscribed'
            ? 'Browser notifications on — click to turn off'
            : 'Enable browser notifications';
    });
}

async function initWebPush() {
    const onStaffOrAdminPage = location.pathname.startsWith('/staff') || location.pathname.startsWith('/admin');
    if (!onStaffOrAdminPage) return;

    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
        return;
    }

    if (Capacitor.isNativePlatform()) return;

    if (Notification.permission === 'denied') {
        updateToggleButtons('denied');
        return;
    }

    // navigator.serviceWorker.ready never resolves if registration failed
    // (see the console.error in app.js) — race it against a timeout so that
    // failure is at least visible instead of leaving the toggle inert forever.
    const registration = await Promise.race([
        navigator.serviceWorker.ready,
        new Promise((_, reject) => setTimeout(() => reject(new Error('service worker not ready after 10s')), 10000)),
    ]).catch((error) => {
        console.error('[webpush] Service worker never became ready:', error);

        return null;
    });

    if (!registration) return;

    let subscription = await registration.pushManager.getSubscription();

    updateToggleButtons(subscription ? 'subscribed' : 'default');

    document.querySelectorAll('[data-web-push-toggle]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            btn.disabled = true;
            try {
                if (subscription) {
                    await disableWebPush(registration);
                    subscription = null;
                    updateToggleButtons('default');
                } else {
                    const ok = await enableWebPush(registration);
                    if (ok) {
                        subscription = await registration.pushManager.getSubscription();
                        updateToggleButtons('subscribed');
                    } else if (Notification.permission === 'denied') {
                        updateToggleButtons('denied');
                    }
                }
            } finally {
                btn.disabled = false;
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initWebPush);
