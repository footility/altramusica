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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable();
            $table->string('tax_code')->nullable();
            $table->enum('status', ['prospect', 'interested', 'enrolled', 'withdrawn'])->default('prospect');
            $table->string('school_origin')->nullable();
            $table->string('how_know_us')->nullable();
            $table->text('preferences')->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->boolean('privacy_consent')->default(false);
            $table->boolean('photo_consent')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
