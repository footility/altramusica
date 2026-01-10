<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->boolean('is_active')->default(true); // true = giorno di lezione, false = sospeso
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['academic_year_id', 'date']);
            $table->index(['academic_year_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_lessons');
    }
};
