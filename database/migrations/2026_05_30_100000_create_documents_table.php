<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('name');                 // nome mostrato alla famiglia
            $table->string('type')->nullable();     // es. Modulo iscrizione, Certificato, Attestato
            $table->string('disk')->default('local'); // disco privato, mai public
            $table->string('path');                 // path su Storage
            $table->unsignedBigInteger('size')->nullable(); // bytes
            $table->string('mime')->nullable();
            $table->boolean('shared_with_family')->default(false); // visibile alla famiglia solo se true
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::destroy('documents');
    }
};
