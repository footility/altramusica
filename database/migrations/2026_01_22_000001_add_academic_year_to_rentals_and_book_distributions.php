<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Deprecated (Fase 1 / T0 schema):
 * This migration existed briefly and may still be present in some local DBs "migrations" table.
 *
 * We now rely on migrate:refresh --seed and keep schema changes in the original create-table
 * migrations (T0). This stub exists only to allow rollback/refresh to run cleanly.
 */
return new class extends Migration
{
    public function up(): void
    {
        // no-op
    }

    public function down(): void
    {
        // no-op
    }
};

