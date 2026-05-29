<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Versione informativa privacy
    |--------------------------------------------------------------------------
    | Quando il testo dell'informativa cambia, incrementare la versione: viene
    | registrata su student_years.privacy_policy_version al momento del consenso,
    | così resta tracciato quale testo l'interessato ha accettato.
    */
    'policy_version' => env('PRIVACY_POLICY_VERSION', '2026-05'),

    /*
    |--------------------------------------------------------------------------
    | Titolare del trattamento
    |--------------------------------------------------------------------------
    | Dati di contatto da mostrare nell'informativa. DA VALIDARE con il
    | consulente privacy del cliente.
    */
    'controller' => [
        'name'    => env('PRIVACY_CONTROLLER_NAME', "Associazione L'Altramusica"),
        'address' => env('PRIVACY_CONTROLLER_ADDRESS', ''),
        'email'   => env('PRIVACY_CONTROLLER_EMAIL', 'privacy@laltramusica.it'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retention dati ex-studenti
    |--------------------------------------------------------------------------
    | Numero di anni dopo il ritiro (status "withdrawn") oltre i quali i dati
    | personali dell'ex-studente vengono anonimizzati dal comando
    | `php artisan privacy:anonymize-withdrawn`.
    |
    | NB: 10 anni è il default prudenziale allineato agli obblighi fiscali
    | (conservazione documenti contabili). DA VALIDARE con il consulente privacy.
    */
    'retention_years' => (int) env('PRIVACY_RETENTION_YEARS', 10),

    /*
    | Testo sostitutivo usato per anonimizzare i campi nominativi.
    */
    'anonymized_placeholder' => 'Anonimizzato',

];
