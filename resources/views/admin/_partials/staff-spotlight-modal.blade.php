{{-- "Staff of the Week / Month" — accessible to every staff member via the
     trophy button in the top nav. Content is fetched on first open (not
     rendered on every page load) since scoring recomputes live. --}}
<div id="pb-staff-spotlight-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-950/60 p-4">
    <div class="w-full max-w-lg pb-card p-6 shadow-2xl max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-black text-slate-900">🏆 Staff Spotlight</h2>
            <button type="button" data-close-staff-spotlight class="text-slate-400 hover:text-slate-700">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
        </div>
        <div data-staff-spotlight-body>
            <p class="text-sm text-slate-400">Loading…</p>
        </div>
    </div>
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('pb-staff-spotlight-modal');
    if (!modal) return;

    const body = modal.querySelector('[data-staff-spotlight-body]');
    let loaded = false;

    const open = () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        if (!loaded) {
            loaded = true;
            fetch('{{ route('admin.staff-spotlight.index') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then((response) => response.text())
                .then((html) => { body.innerHTML = html; })
                .catch(() => {
                    body.innerHTML = '<p class="text-sm text-pink-600">Could not load the leaderboard — please try again.</p>';
                    loaded = false;
                });
        }
    };

    const close = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    document.querySelectorAll('[data-open-staff-spotlight]').forEach((btn) => btn.addEventListener('click', open));
    window.addEventListener('open-staff-spotlight', open);
    modal.querySelector('[data-close-staff-spotlight]')?.addEventListener('click', close);
    modal.addEventListener('click', (event) => { if (event.target === modal) close(); });
});
</script>
@endonce
