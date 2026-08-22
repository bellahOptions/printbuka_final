import './bootstrap';
import Sortable from 'sortablejs';

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

        addBlock(type) {
            if (!blockDefaults[type]) return;
            const block = { id: newBlockId(), type, ...structuredClone(blockDefaults[type]) };
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
