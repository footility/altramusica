<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_years', function (Blueprint $table) {
            // Data del consenso privacy/foto (GDPR: testo + flag + data)
            $table->timestamp('privacy_consent_at')->nullable()->after('privacy_consent');
            $table->timestamp('photo_consent_at')->nullable()->after('photo_consent');
            // Versione dell'informativa accettata al momento del consenso
            $table->string('privacy_policy_version')->nullable()->after('privacy_consent_at');
            // Data di ritiro (valorizzata quando status -> withdrawn): base per la retention
            $table->date('withdrawn_at')->nullable()->after('last_contact_date');
        });

        Schema::table('students', function (Blueprint $table) {
            // Timestamp di anonimizzazione per policy retention ex-studenti
            $table->timestamp('anonymized_at')->nullable()->after('tax_code');
        });
    }

    public function down(): void
    {
        Schema::table('student_years', function (Blueprint $table) {
            $table->dropColumn([
                'privacy_consent_at',
                'photo_consent_at',
                'privacy_policy_version',
                'withdrawn_at',
            ]);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('anonymized_at');
        });
    }
};
