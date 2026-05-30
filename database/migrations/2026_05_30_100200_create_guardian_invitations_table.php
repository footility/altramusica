<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * R13 — Inviti area famiglie: token monouso a scadenza (7 gg di default).
     * La segreteria invita; il tutore attiva impostando password + accettando l'informativa.
     */
    public function up(): void
    {
        Schema::create('guardian_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['guardian_id', 'accepted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardian_invitations');
    }
};
