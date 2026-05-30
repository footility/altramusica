<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * R13 (#8539) — Canale richieste/messaggi dalla famiglia verso il gestionale.
 *
 * Thread interno: la famiglia apre una richiesta (con eventuale studente di
 * riferimento, dentro il proprio perimetro), la segreteria gestisce gli stati
 * e risponde. Niente scritture sulle entità del gestionale: è solo un canale
 * di messaggi tracciato.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('family_requests', function (Blueprint $table) {
            $table->id();
            // Autore: il tutore (account area famiglie). cascade: se sparisce il tutore, spariscono le richieste.
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            // Studente di riferimento (facoltativo): può essere una richiesta generale.
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->default('altro');
            $table->string('subject');
            // Stato gestito dalla segreteria.
            $table->string('status')->default('nuova')->index();
            // Segreteria che ha preso in carico la richiesta.
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            // Ultimo messaggio: per ordinamento inbox e per evidenziare "in attesa".
            $table->timestamp('last_message_at')->nullable();
            $table->string('last_message_role')->nullable(); // family | staff
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['guardian_id', 'status']);
        });

        Schema::create('family_request_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_request_id')->constrained()->cascadeOnDelete();
            // Autore del messaggio (family user o staff). nullOnDelete: si tiene il messaggio anche se l'account sparisce.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_role'); // family | staff
            $table->text('body');
            $table->timestamps();

            $table->index(['family_request_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_request_messages');
        Schema::dropIfExists('family_requests');
    }
};
