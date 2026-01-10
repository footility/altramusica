<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('first_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'converted', 'dismissed'])->default('pending');
            $table->foreignId('student_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            
            $table->index('token');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('first_contacts');
    }
};
