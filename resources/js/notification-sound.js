// Audible chime for staff/admin in-tab notifications (the bell polls every
// 15s — see NotificationBell::render()). Synthesized via Web Audio instead
// of shipping an audio file: a short two-tone "ding" that works everywhere
// without an asset to source or maintain.
function playChime() {
    try {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return;

        const ctx = new Ctx();

        const schedule = () => {
            const now = ctx.currentTime;

            [{ freq: 880, start: 0 }, { freq: 1318.5, start: 0.12 }].forEach(({ freq, start }) => {
                const oscillator = ctx.createOscillator();
                const gain = ctx.createGain();

                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(freq, now + start);

                gain.gain.setValueAtTime(0, now + start);
                gain.gain.linearRampToValueAtTime(0.2, now + start + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, now + start + 0.3);

                oscillator.connect(gain).connect(ctx.destination);
                oscillator.start(now + start);
                oscillator.stop(now + start + 0.32);
            });

            setTimeout(() => ctx.close(), 600);
        };

        // Browsers start a freshly created AudioContext "suspended" until a
        // user gesture unlocks audio for the page — resume() is a no-op once
        // that's already happened, so this is safe to call unconditionally.
        if (ctx.state === 'suspended') {
            ctx.resume().then(schedule).catch(() => {});
        } else {
            schedule();
        }
    } catch (error) {
        console.error('[notification-sound] Failed to play chime:', error);
    }
}

document.addEventListener('livewire:init', () => {
    Livewire.on('play-notification-sound', () => playChime());
});
