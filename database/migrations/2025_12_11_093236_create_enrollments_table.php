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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            // Fase 1 (Master vs Annuale): enrollment punta al corso dell'anno (CourseOffering)
            $table->foreignId('course_offering_id')->constrained('course_offerings')->onDelete('restrict');
            $table->date('enrollment_date');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'suspended', 'completed', 'cancelled'])->default('active');
            $table->text('notes')->nullable();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
