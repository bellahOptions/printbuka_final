<style>
    .pb-icon-field {
        position: relative;
        display: block;
        width: 100%;
    }

    .pb-icon-field > .pb-auto-field-icon {
        pointer-events: none;
        position: absolute;
        left: 0.85rem;
        top: 50%;
        z-index: 2;
        height: 1.1rem;
        width: 1.1rem;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .pb-icon-field > textarea + .pb-auto-field-icon,
    .pb-icon-field:has(textarea) > .pb-auto-field-icon {
        top: 1rem;
        transform: none;
    }
</style>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
<script>
    (() => {
        const excludedTypes = new Set([
            'hidden',
            'checkbox',
            'radio',
            'file',
            'submit',
            'button',
            'reset',
            'image',
            'range',
            'color',
        ]);

        const iconFor = (field) => {
            const tag = field.tagName.toLowerCase();
            const type = (field.getAttribute('type') || tag).toLowerCase();
            const name = `${field.name || ''} ${field.id || ''} ${field.placeholder || ''}`.toLowerCase();

            if (tag === 'textarea') return 'message-square-text';
            if (tag === 'select') return 'list-filter';
            if (type === 'email' || name.includes('email')) return 'mail';
            if (type === 'password' || name.includes('password')) return 'lock-keyhole';
            if (type === 'tel' || name.includes('phone') || name.includes('whatsapp')) return 'phone';
            if (type === 'url' || name.includes('url') || name.includes('link') || name.includes('portfolio')) return 'link';
            if (type === 'date' || type === 'datetime-local' || name.includes('date') || name.includes('deadline')) return 'calendar-days';
            if (type === 'time' || name.includes('time')) return 'clock';
            if (type === 'number' || name.includes('quantity') || name.includes('amount') || name.includes('price') || name.includes('budget')) return 'hash';
            if (type === 'search' || name.includes('search')) return 'search';
            if (name.includes('address') || name.includes('city') || name.includes('state') || name.includes('delivery')) return 'map-pin';
            if (name.includes('company') || name.includes('business')) return 'building-2';
            if (name.includes('name') || name.includes('customer') || name.includes('contact')) return 'user';
            if (name.includes('subject') || name.includes('title') || name.includes('headline')) return 'type';
            if (name.includes('skill') || name.includes('service') || name.includes('product') || name.includes('category')) return 'package';
            return 'circle-dot';
        };

        const shouldSkip = (field) => {
            const type = (field.getAttribute('type') || '').toLowerCase();

            if (excludedTypes.has(type)) return true;
            if (field.dataset.noAutoIcon === 'true') return true;
            if (field.closest('[data-no-auto-icons]')) return true;
            if (field.closest('.pb-icon-field')) return true;
            if (field.closest('[wire\\:id]') && field.closest('[wire\\:id]').querySelector('[data-auto-field-icon]')) return true;

            const parent = field.parentElement;
            if (!parent) return true;

            if (parent.querySelector('[data-auto-field-icon]')) return true;

            return parent.classList.contains('relative')
                && parent.querySelector('[data-lucide], svg') !== null;
        };

        const decorate = (field) => {
            if (shouldSkip(field)) return;

            const wrapper = document.createElement('span');
            wrapper.className = 'pb-icon-field';
            wrapper.dataset.autoIconWrapper = 'true';

            const icon = document.createElement('i');
            icon.className = 'pb-auto-field-icon';
            icon.dataset.autoFieldIcon = 'true';
            icon.dataset.lucide = iconFor(field);

            field.parentNode.insertBefore(wrapper, field);
            wrapper.appendChild(field);
            wrapper.appendChild(icon);

            const currentPadding = Number.parseFloat(window.getComputedStyle(field).paddingLeft || '0');
            if (currentPadding < 38) {
                field.style.paddingLeft = '2.75rem';
            }
        };

        const applyIcons = () => {
            document
                .querySelectorAll('input, select, textarea')
                .forEach(decorate);

            window.lucide?.createIcons();
        };

        document.addEventListener('DOMContentLoaded', applyIcons);
        document.addEventListener('livewire:navigated', applyIcons);
        document.addEventListener('livewire:initialized', () => {
            window.Livewire?.hook?.('morph.updated', applyIcons);
        });
    })();
</script>
