import './bootstrap';
import Sortable from 'sortablejs';
import './rich-media';
import './capacitor-bridge';

/* ─── Idempotency key: one generated per page load, resent unchanged on every
   submit attempt of that form (including retries) so a double-click or a
   slow-network resubmission is recognized server-side as a replay instead of
   creating a duplicate record. Reloading the page gets a fresh key, so a
   genuine second entry is never blocked. See App\Support\IdempotencyGuard. ─── */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-idempotency-key]').forEach((input) => {
        if (!input.value) {
            input.value = window.crypto?.randomUUID ? window.crypto.randomUUID() : `idem_${Date.now()}_${Math.random().toString(36).slice(2, 10)}`;
        }
    });
});

/* ─── Confirm-before-submit for money amounts (data-confirm-amount="<field name>")
   Shows the exact, formatted figure back to the staff member before it's saved,
   so a mistyped amount (e.g. a missing digit or misplaced decimal) is caught
   before it hits the ledger rather than after. Cancelling aborts the submit. ─── */
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !form.dataset.confirmAmount) {
        return;
    }

    const input = form.querySelector(`[name="${form.dataset.confirmAmount}"]`);
    const amount = input ? parseFloat(input.value) : NaN;
    if (Number.isNaN(amount)) {
        return;
    }

    const formatted = '₦' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const label = form.dataset.confirmLabel || 'this entry';

    if (!window.confirm(`Confirm ${label}\n\nAmount: ${formatted}\n\nDouble-check this figure matches what you intended before saving.`)) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
}, true);

/* ─── Staff PWA / Capacitor shell: service worker registration ─── */
if ('serviceWorker' in navigator && (location.pathname.startsWith('/staff') || location.pathname.startsWith('/admin'))) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/staff-sw.js').catch(() => {});
    });
}

/* ─── Popover (data-popover-btn / data-popover-panel) ─── */
document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-popover-btn]');
        if (btn) {
            const wrapper = btn.closest('.pb-popover-wrapper');
            const panel   = wrapper?.querySelector('[data-popover-panel]');
            if (!panel) return;
            const isOpen = !panel.classList.contains('hidden');
            // Close all other open popovers first
            document.querySelectorAll('[data-popover-panel]:not(.hidden)')
                .forEach(p => p.classList.add('hidden'));
            if (!isOpen) panel.classList.remove('hidden');
            return;
        }
        // Click outside any wrapper → close all
        if (!e.target.closest('.pb-popover-wrapper')) {
            document.querySelectorAll('[data-popover-panel]:not(.hidden)')
                .forEach(p => p.classList.add('hidden'));
        }
    });
});

const disableSubmittingForm = (form, submitter) => {
    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    if (form.dataset.submitting === 'true') {
        return;
    }

    form.dataset.submitting = 'true';
    form.setAttribute('aria-busy', 'true');

    const submitButtons = Array.from(
        form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])')
    );

    submitButtons.forEach((button) => {
        if (!(button instanceof HTMLButtonElement || button instanceof HTMLInputElement)) {
            return;
        }

        button.disabled = true;
        button.setAttribute('aria-disabled', 'true');
        button.classList.add('cursor-not-allowed', 'opacity-70');
    });

    const activeSubmitter = submitter instanceof HTMLButtonElement || submitter instanceof HTMLInputElement
        ? submitter
        : submitButtons[0];

    if (!activeSubmitter) {
        return;
    }

    activeSubmitter.style.minWidth = `${activeSubmitter.offsetWidth}px`;

    if (activeSubmitter instanceof HTMLInputElement) {
        activeSubmitter.dataset.originalValue = activeSubmitter.value;
        activeSubmitter.value = activeSubmitter.dataset.submitLabel || 'Please wait...';

        return;
    }

    if (activeSubmitter.textContent?.trim()) {
        activeSubmitter.dataset.originalText = activeSubmitter.textContent;
        activeSubmitter.textContent = activeSubmitter.dataset.submitLabel || 'Please wait...';
    }
};

document.addEventListener('submit', (event) => {
    const form = event.target;

    window.queueMicrotask(() => {
        if (event.defaultPrevented) {
            return;
        }

        disableSubmittingForm(form, event.submitter);
    });
});

/* ─── Drag-and-drop email block builder (Alpine.data component) ─── */
const blockDefaults = {
    heading: { text: 'New heading', size: 'md' },
    paragraph: { text: 'Write your message here…' },
    image: { url: '', alt: '' },
    button: { label: 'Click here', url: 'https://' },
    divider: {},
    spacer: { height: 16 },
};

const newBlockId = () => (window.crypto?.randomUUID ? window.crypto.randomUUID() : `blk_${Date.now()}_${Math.random().toString(36).slice(2, 8)}`);

document.addEventListener('alpine:init', () => {
    window.Alpine.data('emailBlockBuilder', (initialBlocks = []) => ({
        blocks: Array.isArray(initialBlocks) && initialBlocks.length
            ? initialBlocks.map((block) => ({ id: block.id || newBlockId(), ...block }))
            : [],
        selectedId: null,

        init() {
            this.$watch('blocks', () => {
                this.$el.dispatchEvent(new CustomEvent('blocks-changed', { bubbles: true }));
            });
        },

        addBlock(type) {
            if (!blockDefaults[type]) return;
            const block = { id: newBlockId(), type, ...blockDefaults[type] };
            this.blocks.push(block);
            this.selectedId = block.id;
        },

        removeBlock(id) {
            this.blocks = this.blocks.filter((block) => block.id !== id);
            if (this.selectedId === id) this.selectedId = null;
        },

        selectBlock(id) {
            this.selectedId = this.selectedId === id ? null : id;
        },

        get selected() {
            return this.blocks.find((block) => block.id === this.selectedId) || null;
        },

        initSortable(el) {
            Sortable.create(el, {
                handle: '.block-drag-handle',
                animation: 150,
                onEnd: (evt) => {
                    if (evt.oldIndex === evt.newIndex) return;
                    const moved = this.blocks.splice(evt.oldIndex, 1)[0];
                    this.blocks.splice(evt.newIndex, 0, moved);
                },
            });
        },

        serialize() {
            return JSON.stringify(this.blocks);
        },
    }));
});

/**
 * Wires a live, auto-refreshing inline preview for a drag-and-drop block
 * builder page. Listens for 'blocks-changed' bubbling from any canvas on the
 * page (one or more `emailBlockBuilder` instances), debounces, and re-fetches
 * server-rendered HTML into an <iframe> — no manual "Preview" click needed.
 * The server (not this JS) remains the single source of truth for how blocks
 * render, so this never duplicates App\Support\EmailBlockRenderer's logic.
 *
 * @param {string} iframeSelector - CSS selector for the target <iframe>.
 * @param {() => string} buildPreviewUrl - returns the current preview URL
 *   (reads whatever hidden block-JSON inputs exist on the page at call time).
 */
/* ─── Homepage: hero slider (.hero-slide / .hero-dot) ─── */
document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.hero-dot');
    if (!slides.length || !dots.length) return;

    let current = 0;
    let timer;

    function goTo(index) {
        slides[current].classList.remove('opacity-100');
        slides[current].classList.add('opacity-0');
        dots[current].classList.remove('active', 'w-6');
        dots[current].classList.add('w-2');
        current = (index + slides.length) % slides.length;
        slides[current].classList.remove('opacity-0');
        slides[current].classList.add('opacity-100');
        dots[current].classList.add('active', 'w-6');
        dots[current].classList.remove('w-2');
        clearInterval(timer);
        timer = setInterval(() => goTo(current + 1), 5500);
    }

    dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));
    timer = setInterval(() => goTo(current + 1), 5500);
});

/* ─── Homepage: featured products carousel (#fp-track) ─── */
document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('fp-track');
    if (!track) return;

    const prevBtn = document.getElementById('fp-prev');
    const nextBtn = document.getElementById('fp-next');
    const dotsEl = document.getElementById('fp-dots');
    const perPage = 4;
    const slides = Array.from(track.querySelectorAll('.fp-slide'));
    const total = slides.length;
    const pages = Math.ceil(total / perPage);
    let current = 0;

    if (total === 0) return;

    // Build dots
    for (let i = 0; i < pages; i++) {
        const d = document.createElement('button');
        d.className = 'w-2.5 h-2.5 rounded-full bg-gray-300 transition-colors';
        d.setAttribute('aria-label', 'Go to page ' + (i + 1));
        d.addEventListener('click', () => goTo(i));
        dotsEl.appendChild(d);
    }

    function goTo(page) {
        current = Math.max(0, Math.min(page, pages - 1));
        const slideWidth = slides[0].offsetWidth + 24; // 24 = gap-6
        track.style.transform = 'translateX(-' + (current * perPage * slideWidth) + 'px)';
        prevBtn.disabled = current === 0;
        nextBtn.disabled = current === pages - 1;
        dotsEl.querySelectorAll('button').forEach((d, i) => {
            d.classList.toggle('bg-[#EC268F]', i === current);
            d.classList.toggle('bg-gray-300', i !== current);
        });
    }

    prevBtn.addEventListener('click', () => goTo(current - 1));
    nextBtn.addEventListener('click', () => goTo(current + 1));

    goTo(0);
});

/* ─── Homepage: category carousel (#cat-track) ─── */
document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('cat-track');
    if (!track) return;

    const prevBtn = document.getElementById('cat-prev');
    const nextBtn = document.getElementById('cat-next');
    const dotsEl = document.getElementById('cat-dots');
    const perPage = 3;
    const slides = Array.from(track.querySelectorAll('.cat-slide'));
    const total = slides.length;
    const pages = Math.ceil(total / perPage);
    let current = 0;

    if (total === 0) return;

    for (let i = 0; i < pages; i++) {
        const d = document.createElement('button');
        d.className = 'w-2.5 h-2.5 rounded-full bg-slate-300 transition-colors';
        d.setAttribute('aria-label', 'Go to page ' + (i + 1));
        d.addEventListener('click', () => catGoTo(i));
        dotsEl.appendChild(d);
    }

    function catGoTo(page) {
        current = Math.max(0, Math.min(page, pages - 1));
        const slideWidth = slides[0].offsetWidth + 24;
        track.style.transform = 'translateX(-' + (current * perPage * slideWidth) + 'px)';
        prevBtn.disabled = current === 0;
        nextBtn.disabled = current === pages - 1;
        dotsEl.querySelectorAll('button').forEach((d, i) => {
            d.classList.toggle('bg-[#EC268F]', i === current);
            d.classList.toggle('bg-slate-300', i !== current);
        });
    }

    prevBtn.addEventListener('click', () => catGoTo(current - 1));
    nextBtn.addEventListener('click', () => catGoTo(current + 1));
    catGoTo(0);
});

/* ─── Homepage: lazy-load specialist service videos (video.svc-video[data-src]) ─── */
document.addEventListener('DOMContentLoaded', () => {
    const videos = document.querySelectorAll('video.svc-video[data-src]');
    if (!videos.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const v = entry.target;
            v.src = v.dataset.src;
            v.load();
            v.play().catch(() => {});
            observer.unobserve(v);
        });
    }, { threshold: 0.15 });

    videos.forEach((v) => observer.observe(v));
});

window.wireLivePreview = function wireLivePreview(iframeSelector, buildPreviewUrl) {
    const iframe = document.querySelector(iframeSelector);
    if (!iframe) return;

    let debounceTimer = null;
    let controller = null;

    const refresh = () => {
        if (controller) controller.abort();
        controller = new AbortController();

        fetch(buildPreviewUrl(), { signal: controller.signal })
            .then((response) => response.text())
            .then((html) => { iframe.srcdoc = html; })
            .catch((error) => {
                if (error.name !== 'AbortError') console.error('Live preview failed to refresh.', error);
            });
    };

    document.addEventListener('blocks-changed', () => {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(refresh, 400);
    });

    refresh();
};

/* ─── Attendance: geolocation + low-res selfie capture on clock in/out ─── */
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('[data-attendance-form]');
    if (!forms.length) return;

    const MAX_PHOTO_WIDTH = 480;
    const PHOTO_QUALITY = 0.6;

    const downscalePhoto = (file) => new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);

        img.onload = () => {
            const scale = Math.min(1, MAX_PHOTO_WIDTH / img.width);
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(img.width * scale);
            canvas.height = Math.round(img.height * scale);

            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            URL.revokeObjectURL(url);

            canvas.toBlob((blob) => (blob ? resolve(blob) : reject(new Error('Could not process photo.'))), 'image/jpeg', PHOTO_QUALITY);
        };
        img.onerror = () => { URL.revokeObjectURL(url); reject(new Error('Could not read photo.')); };
        img.src = url;
    });

    const getLocation = () => new Promise((resolve) => {
        // Inside the Capacitor shell, the native Geolocation plugin handles OS
        // permission prompts more reliably than the raw browser API embedded
        // in a WebView. Fall back to the browser API everywhere else (plain
        // mobile/desktop browser, or if the plugin isn't present).
        if (window.pbNativeGeolocation) {
            window.pbNativeGeolocation().then(resolve);
            return;
        }

        if (!navigator.geolocation) { resolve(null); return; }

        navigator.geolocation.getCurrentPosition(
            (position) => resolve({ lat: position.coords.latitude, lng: position.coords.longitude }),
            () => resolve(null), // denied/unavailable — server still accepts the punch, just unverified
            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
        );
    });

    forms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) { submitButton.disabled = true; submitButton.textContent = 'Getting location…'; }

            const statusEl = form.querySelector('[data-attendance-status]');
            if (statusEl) statusEl.textContent = 'Getting your location…';

            const location = await getLocation();
            const formData = new FormData(form);
            if (location) {
                formData.set('lat', location.lat);
                formData.set('lng', location.lng);
            }

            const photoInput = form.querySelector('input[type="file"][data-attendance-photo]');
            if (photoInput?.files?.[0]) {
                if (statusEl) statusEl.textContent = 'Processing photo…';
                try {
                    const downscaled = await downscalePhoto(photoInput.files[0]);
                    formData.set('photo', downscaled, 'attendance.jpg');
                } catch {
                    formData.delete('photo');
                }
            }

            if (submitButton) submitButton.textContent = 'Submitting…';
            if (statusEl) statusEl.textContent = 'Submitting…';

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            }).then(() => {
                window.location.reload();
            }).catch(() => {
                if (statusEl) statusEl.textContent = 'Something went wrong — please try again.';
                if (submitButton) { submitButton.disabled = false; submitButton.textContent = submitButton.dataset.originalLabel || 'Retry'; }
            });
        });
    });
});
