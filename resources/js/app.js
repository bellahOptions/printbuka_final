import './bootstrap';

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
