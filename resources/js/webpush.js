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

function postJson(url, method, body) {
    return fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(body),
    }).catch(() => {});
}

async function enableWebPush(registration) {
    const key = vapidKey();
    if (!key) return false;

    const permission = await Notification.requestPermission();
    if (permission !== 'granted') return false;

    const subscription = await registration.pushManager.subscribe({
        userVisibleOnly: true,
        applicationServerKey: urlBase64ToUint8Array(key),
    });

    const url = subscribeUrl();
    if (url) await postJson(url, 'POST', subscription.toJSON());

    return true;
}

async function disableWebPush(registration) {
    const subscription = await registration.pushManager.getSubscription();
    if (!subscription) return;

    const url = unsubscribeUrl();
    if (url) await postJson(url, 'DELETE', { endpoint: subscription.endpoint });

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

    const registration = await navigator.serviceWorker.ready;
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
