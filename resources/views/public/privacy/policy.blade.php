@extends('public.privacy.layout', ['title' => 'Informativa privacy'])

@section('heading', 'Informativa sul trattamento dei dati personali')

@section('content')
    <p class="text-muted">Ai sensi degli artt. 13-14 del Regolamento (UE) 2016/679 (GDPR).</p>

    <h2 class="h5 mt-4">1. Titolare del trattamento</h2>
    <p>
        {{ config('privacy.controller.name') }}@if(config('privacy.controller.address')), {{ config('privacy.controller.address') }}@endif.
        Email: <a href="mailto:{{ config('privacy.controller.email') }}">{{ config('privacy.controller.email') }}</a>.
    </p>

    <h2 class="h5 mt-4">2. Dati trattati</h2>
    <p>
        Dati anagrafici e di contatto dell'allievo e dell'eventuale genitore/tutore (nome, cognome,
        data di nascita, codice fiscale, recapiti), dati relativi all'iscrizione, ai corsi, ai pagamenti,
        ai contratti e — ove prestato apposito consenso — immagini/foto a fini documentali e promozionali.
    </p>

    <h2 class="h5 mt-4">3. Finalità e base giuridica</h2>
    <ul>
        <li>Gestione dell'iscrizione e dei servizi didattici — esecuzione del contratto (art. 6.1.b).</li>
        <li>Adempimenti amministrativi, contabili e fiscali — obbligo legale (art. 6.1.c).</li>
        <li>Uso di immagini/foto a fini promozionali — consenso dell'interessato (art. 6.1.a), revocabile.</li>
    </ul>

    <h2 class="h5 mt-4">4. Conservazione dei dati</h2>
    <p>
        I dati sono conservati per la durata del rapporto e, dopo il ritiro/cessazione, per il tempo
        necessario agli adempimenti di legge. Decorsi <strong>{{ config('privacy.retention_years') }} anni</strong>
        dal ritiro, i dati personali dell'ex-allievo vengono anonimizzati o cancellati, salvo diversi
        obblighi normativi.
    </p>

    <h2 class="h5 mt-4">5. Comunicazione dei dati</h2>
    <p>
        I dati possono essere comunicati a soggetti terzi (es. consulenti, commercialista, fornitori di
        servizi tecnici) esclusivamente per le finalità sopra indicate e nominati responsabili del
        trattamento ove necessario. Non è previsto trasferimento extra-UE.
    </p>

    <h2 class="h5 mt-4">6. Diritti dell'interessato</h2>
    <p>
        L'interessato può esercitare i diritti di accesso, rettifica, cancellazione, limitazione,
        portabilità e opposizione (artt. 15-22 GDPR), nonché revocare i consensi prestati, scrivendo a
        <a href="mailto:{{ config('privacy.controller.email') }}">{{ config('privacy.controller.email') }}</a>.
        È inoltre possibile proporre reclamo all'Autorità Garante per la protezione dei dati personali.
    </p>
@endsection
