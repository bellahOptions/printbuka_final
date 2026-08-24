# Printbuka Staff App (Capacitor)

This is a thin native shell around the **existing** staff/admin portal — it does
not duplicate any UI or backend logic. `capacitor.config.json` points the
app's WebView at the live Laravel site over HTTPS, so the app always talks to
the exact same server and the exact same central database the web portal
already uses. There is no separate mobile backend, no data sync, and no
second database to keep in sync.

```
Native shell (this project)  ──HTTPS──▶  Laravel app (printbuka_final)  ──▶  MySQL (cPanel)
        Capacitor WebView                same Blade/Livewire pages,
                                          same session auth, same DB
```

## What's already wired on the Laravel side

- `resources/js/capacitor-bridge.js` (bundled into the main app's `app.js`)
  detects when it's running inside this native shell (`Capacitor.isNativePlatform()`)
  and is a complete no-op everywhere else (plain browser, customer portal).
- **Push notifications**: on first load inside the app, it requests permission,
  registers for FCM, and POSTs the device token to `POST /admin/devices`
  (`admin.devices.store`) — a session-authenticated route that reuses the
  existing `App\Http\Controllers\Api\StaffDeviceController` (the same one the
  Sanctum mobile API already used), so it lands in the same `staff_push_subscriptions`
  table the existing Firebase-push sending code already reads from. No new
  server-side push logic was needed.
- **Geolocation**: the existing attendance clock-in/out flow
  (`resources/js/app.js`, `getLocation()`) now prefers the native
  `@capacitor/geolocation` plugin when running in the app (more reliable OS
  permission handling than raw browser geolocation inside a WebView), and
  falls back to the browser API everywhere else. Nothing else about the
  attendance flow changed.
- **PWA**: `public/staff-manifest.json` + `public/staff-sw.js` make the staff
  portal installable from a normal mobile browser too (separate from this
  native shell — a nice fallback for anyone who can't install the app-store
  build yet).

## What you need to do before this can ship

1. **Set the real production domain.** Edit `capacitor.config.json` →
   `server.url` (currently a placeholder) to your live HTTPS staff login URL,
   e.g. `https://portal.printbuka.com/staff/login`. This *must* be HTTPS in
   production — Capacitor's Android WebView blocks mixed content by default
   (`allowMixedContent: false` is set deliberately; don't turn this on for a
   real deployment). After changing it, run `npm run sync` in this directory.

2. **Register an Android app in your existing Firebase project** (the one
   whose service-account JSON is already configured server-side via
   `FIREBASE_CREDENTIALS`) with package name `com.printbuka.staff`, download
   the resulting `google-services.json`, and place it at
   `android/app/google-services.json`. Without this file the app builds fine
   but push notifications silently won't register (the Gradle config already
   detects its absence and skips the Firebase plugin rather than failing the
   build — you'll just see `google-services.json not found, google-services
   plugin not applied. Push Notifications won't work` in the build log).

3. **Build & sign the Android app.**
   ```bash
   cd mobile-app
   npm install
   npm run sync            # copies config + plugin changes into android/
   npm run android:build-debug   # unsigned debug APK, for testing on a device
   # or: npm run android:open    # opens Android Studio for a signed release build
   ```
   A release build for the Play Store needs a signing keystore — generate one
   with `keytool` and configure it in `android/app/build.gradle`; this wasn't
   done here since a keystore is a secret you should generate and hold
   yourself, not something to have an agent create for you.

4. **iOS needs a Mac.** `npx cap add ios` (not run here — this sandbox is
   Linux) generates an Xcode project the same way `add android` did; building,
   signing, and submitting it requires Xcode and an Apple Developer account,
   which only run on macOS. Everything on the Laravel/JS side above already
   works for iOS too (Capacitor's push/geolocation plugins are cross-platform)
   — only the native Xcode project and its own `GoogleService-Info.plist`
   (the iOS equivalent of `google-services.json`) are outstanding.

## Why "remote URL" mode instead of bundling the site

Capacitor can either bundle static web assets into the app, or point the
WebView at a live URL. Bundling doesn't fit here: this app is server-rendered
PHP (Blade + Livewire), not a static SPA build — there's no meaningful
"frontend build" to bundle that would work without the live backend right
next to it. Pointing at the real domain means every deploy of the web app is
instantly live in the native app too, with zero app-store resubmission for
ordinary content/logic changes — you only need to resubmit when native
config itself changes (new permissions, new plugins, icon/splash changes).
