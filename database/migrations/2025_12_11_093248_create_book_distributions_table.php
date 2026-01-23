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
        Schema::create('book_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('restrict');
            // Year-scoped (Master vs Annual): distributions belong to an academic year
            // Column only here (academic_years table is created later); FK is added in academic_years migration.
            $table->unsignedBigInteger('academic_year_id')->nullable()->index();
            // Phase 1 (Master vs Annual): if linked to a course, link to the annual offering
            $table->foreignId('course_offering_id')->nullable()->constrained('course_offerings')->nullOnDelete();
            $table->date('distribution_date');
            $table->integer('quantity')->default(1);
            $table->decimal('price_paid', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_distributions');
    }
};
