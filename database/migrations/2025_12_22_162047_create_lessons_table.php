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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            // Fase 1 (Master vs Annuale): lezione legata al corso dell'anno (CourseOffering)
            $table->foreignId('course_offering_id')->constrained('course_offerings')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->foreignId('substitute_teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            // classroom_id aggiunto in migration successiva
            $table->date('date');
            $table->time('time_start');
            $table->time('time_end');
            $table->text('notes')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();
            
            $table->index(['course_offering_id', 'date']);
            $table->index(['teacher_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
