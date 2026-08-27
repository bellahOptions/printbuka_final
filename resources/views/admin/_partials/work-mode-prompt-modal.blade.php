{{-- One-time prompt asking a staff member to declare their work
     arrangement: onsite, hybrid, or fully remote. Drives whether attendance
     clock-in enforces the office geofence for them. Editable later via the
     same fields on the attendance page. --}}
<div id="pb-work-mode-modal" class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/60 p-4">
    <div class="w-full max-w-md pb-card p-6 shadow-2xl">
        <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">One quick thing</p>
        <h2 class="mt-1 text-lg font-bold text-slate-900">How do you work?</h2>
        <p class="mt-2 text-sm text-slate-600">Tell us your work arrangement so attendance clock-in knows whether to expect you at the office.</p>

        <form method="POST" action="{{ route('admin.staff.work-mode.update') }}" class="mt-5 space-y-4" data-work-mode-form>
            @csrf
            @include('admin._partials.work-mode-fields', ['profile' => null])

            <button type="submit" class="pb-btn pb-btn-primary w-full">Save my work arrangement</button>
            <button type="button" data-close-work-mode-modal class="w-full text-center text-xs font-bold text-slate-400 hover:text-slate-600">
                Remind me later
            </button>
        </form>
    </div>
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-work-mode-form]').forEach((form) => {
        const daysWrap = form.querySelector('[data-onsite-days-wrap]');
        const syncDaysVisibility = () => {
            const selected = form.querySelector('[data-work-mode-option]:checked')?.value;
            daysWrap?.classList.toggle('hidden', selected !== 'hybrid');
        };

        form.querySelectorAll('[data-work-mode-option]').forEach((input) => {
            input.addEventListener('change', syncDaysVisibility);
        });
    });

    document.getElementById('pb-work-mode-modal')
        ?.querySelector('[data-close-work-mode-modal]')
        ?.addEventListener('click', () => document.getElementById('pb-work-mode-modal')?.remove());
});
</script>
@endonce
