@if (\App\Support\Turnstile::enabled() && filled(\App\Support\Turnstile::siteKey()))
    <script>
        window.printbukaTurnstileSiteKey = @json(\App\Support\Turnstile::siteKey());
        window.printbukaRenderTurnstile = () => {
            if (!window.turnstile || !window.printbukaTurnstileSiteKey) {
                return;
            }

            document.querySelectorAll('form[method="POST"], form[method="post"]').forEach((form) => {
                let widget = form.querySelector('.cf-turnstile');

                if (!widget) {
                    const submit = form.querySelector('[type="submit"]');
                    widget = document.createElement('div');
                    widget.className = 'cf-turnstile';
                    widget.dataset.sitekey = window.printbukaTurnstileSiteKey;

                    if (submit?.parentNode) {
                        submit.parentNode.insertBefore(widget, submit);
                    } else {
                        form.appendChild(widget);
                    }
                }

                if (widget.dataset.turnstileRendered !== '1') {
                    widget.dataset.turnstileRendered = '1';
                    window.turnstile.render(widget);
                }
            });
        };
    </script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=printbukaRenderTurnstile" async defer></script>
@endif
