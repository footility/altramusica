<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * R13 — Fail-safe: un documento entra nell'area famiglie SOLO se esplicitamente
     * condiviso dalla segreteria. Default false: nessun documento esposto senza scelta.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->boolean('visible_to_family')->default(false)->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('visible_to_family');
        });
    }
};
