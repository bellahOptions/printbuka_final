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

    const toLocationResult = (position) => ({
        lat: position.coords.latitude,
        lng: position.coords.longitude,
        accuracy: Number.isFinite(position.coords.accuracy) ? Math.round(position.coords.accuracy) : null,
    });

    window.pbNativeGeolocation = async () => {
        try {
            const permission = await Geolocation.requestPermissions();
            if (permission.location !== 'granted' && permission.coarseLocation !== 'granted') {
                return null;
            }

            try {
                // First try: a fresh, high-accuracy GPS fix — best case outdoors.
                return toLocationResult(await Geolocation.getCurrentPosition({ enableHighAccuracy: true, timeout: 15000 }));
            } catch {
                // GPS often can't get a satellite lock indoors (e.g. inside the
                // office itself) and times out — and a device where the staff
                // member only granted "approximate location" (coarseLocation)
                // can't get a GPS fix at all. Fall back to network/WiFi-based
                // positioning, which is faster and works far better indoors, at
                // the cost of some precision — still far better than failing
                // the clock-in outright for staff who are genuinely on site.
                return toLocationResult(await Geolocation.getCurrentPosition({ enableHighAccuracy: false, timeout: 10000 }));
            }
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
