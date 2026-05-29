@extends('public.privacy.layout', ['title' => 'Cookie policy'])

@section('heading', 'Cookie policy')

@section('content')
    <p>
        Il gestionale è un'applicazione ad accesso riservato e utilizza esclusivamente
        <strong>cookie tecnici essenziali</strong>, necessari al funzionamento e alla sicurezza del
        servizio. Non vengono utilizzati cookie di profilazione o di terze parti a fini di marketing.
    </p>

    <h2 class="h5 mt-4">Cookie utilizzati</h2>
    <table class="table table-sm">
        <thead>
            <tr><th>Nome</th><th>Finalità</th><th>Durata</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><code>{{ config('session.cookie') }}</code></td>
                <td>Mantenimento della sessione utente autenticata.</td>
                <td>Sessione</td>
            </tr>
            <tr>
                <td><code>XSRF-TOKEN</code></td>
                <td>Protezione dagli attacchi CSRF.</td>
                <td>Sessione</td>
            </tr>
            <tr>
                <td><code>cookie_consent_ack</code></td>
                <td>Memorizza la presa visione del banner cookie.</td>
                <td>1 anno (localStorage)</td>
            </tr>
        </tbody>
    </table>

    <p class="small text-muted">
        Trattandosi di soli cookie tecnici, non è richiesto il consenso preventivo: il banner ha
        funzione meramente informativa.
    </p>
@endsection
