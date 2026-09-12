// Browser Web Push for staff/admin pages viewed in a plain browser tab (the
// Capacitor-wrapped shell already gets native push via capacitor-bridge.js,
// so this deliberately no-ops there to avoid double-registering).
//
// Mandatory by design: staff/admins cannot turn this off from within the
// app — there is no toggle and no unsubscribe endpoint. Once the browser
// grants notification permission (a one-time browser-level prompt outside
// our control), this subscribes and stays subscribed for as long as the
// browser allows it.
import { Capacitor } from '@capacitor/core';

const vapidKey = () => document.querySelector('meta[name="vapid-public-key"]')?.content || '';
const subscribeUrl = () => document.querySelector('meta[name="web-push-subscribe-url"]')?.content;

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map((c) => c.charCodeAt(0)));
}

async function postJson(url, body) {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        });

        if (!response.ok) {
            console.error(`[webpush] POST ${url} failed with status ${response.status}`);

            return false;
        }

        return true;
    } catch (error) {
        console.error(`[webpush] POST ${url} failed:`, error);

        return false;
    }
}

async function enableWebPush(registration) {
    const key = vapidKey();
    if (!key) {
        console.error('[webpush] No VAPID public key found in the page — check VAPID_PUBLIC_KEY in .env.');

        return;
    }

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        console.warn('[webpush] Notification permission was not granted:', permission);

        return;
    }

    const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(key),
    });

    const url = subscribeUrl();
    if (!url) {
        console.error('[webpush] No subscribe URL found in the page meta tags.');
        await subscription.unsubscribe();

        return;
    }

    const saved = await postJson(url, subscription.toJSON());
    if (!saved) {
        // The browser is subscribed but the server doesn't know it — that's
        // worse than not subscribing at all (looks "on" but never fires),
        // so undo the browser-side subscription too; it'll retry next load.
        await subscription.unsubscribe();
    }
}

async function initWebPush() {
    const onStaffOrAdminPage = location.pathname.startsWith('/staff') || location.pathname.startsWith('/admin');
    if (!onStaffOrAdminPage) return;

    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
        return;
    }

    if (Capacitor.isNativePlatform()) return;

    if (Notification.permission === 'denied') {
        // Blocked at the browser/OS level — nothing the app can do about
        // that short of the staff member changing their browser's site
        // settings; there is no in-app fallback or toggle to work around it.
        return;
    }

    // navigator.serviceWorker.ready never resolves if registration failed
    // (see the console.error in app.js) — race it against a timeout so that
    // failure is at least visible instead of hanging forever.
    const registration = await Promise.race([
        navigator.serviceWorker.ready,
        new Promise((_, reject) => setTimeout(() => reject(new Error('service worker not ready after 10s')), 10000)),
    ]).catch((error) => {
        console.error('[webpush] Service worker never became ready:', error);

        return null;
    });

    if (!registration) return;

    const existing = await registration.pushManager.getSubscription();
    if (existing) return;

    await enableWebPush(registration);
}

document.addEventListener('DOMContentLoaded', initWebPush);
