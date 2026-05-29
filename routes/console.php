<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Retention GDPR: anonimizza ex-studenti ritirati da oltre N anni (config/privacy.php).
Schedule::command('privacy:anonymize-withdrawn')->monthlyOn(1, '03:00');
