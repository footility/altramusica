<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * R13 — Area famiglie (approccio A del design #8536): un `User` con ruolo `family`
     * collegato al `Guardian`. Niente guard separato: si riusa l'auth web + Spatie,
     * isolando l'area via ruolo + middleware + prefix `famiglia`.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('guardian_id')->nullable()->after('id')
                ->constrained('guardians')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('guardian_id');
        });
    }
};
