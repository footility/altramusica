{{-- Cookie banner essenziale: solo cookie tecnici, informativa (no profilazione) --}}
<div id="cookie-banner"
     class="position-fixed bottom-0 start-0 end-0 m-0 p-3 bg-dark text-white shadow-lg"
     style="z-index: 1080; display: none;">
    <div class="container d-flex flex-column flex-md-row align-items-md-center gap-2">
        <div class="small flex-grow-1">
            Questo gestionale utilizza solo <strong>cookie tecnici essenziali</strong> per il
            funzionamento e la sicurezza. Nessun cookie di profilazione.
            <a href="{{ route('privacy.cookies') }}" class="text-info">Cookie policy</a> ·
            <a href="{{ route('privacy.policy') }}" class="text-info">Privacy policy</a>.
        </div>
        <button type="button" id="cookie-banner-accept" class="btn btn-sm btn-light flex-shrink-0">
            Ho capito
        </button>
    </div>
</div>
<script>
    (function () {
        var KEY = 'cookie_consent_ack';
        var banner = document.getElementById('cookie-banner');
        if (!banner) return;
        try {
            if (!localStorage.getItem(KEY)) {
                banner.style.display = 'block';
            }
        } catch (e) {
            banner.style.display = 'block';
        }
        var btn = document.getElementById('cookie-banner-accept');
        if (btn) {
            btn.addEventListener('click', function () {
                try { localStorage.setItem(KEY, new Date().toISOString()); } catch (e) {}
                banner.style.display = 'none';
            });
        }
    })();
</script>
