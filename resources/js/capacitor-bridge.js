// Bridges the Capacitor native shell (see mobile-app/) to this same web app's
// existing session-authenticated staff pages. This file is part of the main
// Laravel app's JS bundle — it only does anything when that bundle happens to
// be running inside the Capacitor WebView (window.Capacitor present), and is
// an inert no-op in a plain browser or the customer portal.
import { Capacitor } from '@capacitor/core';

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
const devicesUrl = () => document.querySelector('meta[name="staff-devices-url"]')?.content;

function registerPushDevice(token) {
    const url = devicesUrl();
    if (!url) return;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ device_token: token, platform: Capacitor.getPlatform() }),
    }).catch(() => {});
}

async function initPushNotifications() {
    const { PushNotifications } = await import('@capacitor/push-notifications');

    const permission = await PushNotifications.requestPermissions();
    if (permission.receive !== 'granted') return;

    await PushNotifications.register();

    PushNotifications.addListener('registration', (token) => {
        registerPushDevice(token.value);
    });

    // Surfacing a push while the app is foregrounded is a native-shell concern
    // (system notification vs in-app banner) — left to the OS default for now.
    PushNotifications.addListener('registrationError', () => {});
}

async function initNativeGeolocation() {
    const { Geolocation } = await import('@capacitor/geolocation');

    window.pbNativeGeolocation = async () => {
        try {
            const permission = await Geolocation.requestPermissions();
            if (permission.location !== 'granted' && permission.coarseLocation !== 'granted') {
                return null;
            }

            const position = await Geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 8000 });

            return { lat: position.coords.latitude, lng: position.coords.longitude };
        } catch {
            return null;
        }
    };
}

if (Capacitor.isNativePlatform()) {
    document.addEventListener('DOMContentLoaded', () => {
        initPushNotifications();
        initNativeGeolocation();
    });
}
