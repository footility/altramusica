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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_type_id')->constrained()->onDelete('restrict');
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Fase 1 (Master vs Annuale): corso dell'anno = CourseOffering (tariffe/orari/docente variabili per anno)
        // NOTE: academic_year_id viene aggiunto in migration successiva (create_academic_years_table) per ordine migrazioni.
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('restrict');
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            // classroom_id: FK added later (classrooms table is created after courses)
            $table->unsignedBigInteger('classroom_id')->nullable()->index();

            // academic_year_id added later (FK in academic_years migration)
            $table->unsignedBigInteger('academic_year_id')->nullable()->index();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();

            $table->integer('max_students')->nullable();
            $table->integer('current_students')->default(0);
            $table->enum('status', ['planned', 'active', 'completed', 'cancelled'])->default('planned');

            $table->decimal('price_per_lesson', 10, 2)->default(0);
            $table->integer('lessons_per_week')->default(1);
            $table->integer('weeks_per_year')->default(36);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['course_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
        Schema::dropIfExists('courses');
    }
};
