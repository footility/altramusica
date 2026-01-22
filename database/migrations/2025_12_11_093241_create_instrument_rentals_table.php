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
        Schema::create('instrument_rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('instrument_id')->constrained()->onDelete('restrict');
            // Year-scoped (Master vs Annual): rentals belong to an academic year
            // Column only here (academic_years table is created later); FK is added in academic_years migration.
            $table->unsignedBigInteger('academic_year_id')->nullable()->index();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->decimal('deposit', 10, 2)->default(0);
            $table->enum('status', ['active', 'returned', 'cancelled'])->default('active');
            $table->date('return_date')->nullable();
            $table->enum('return_condition', ['excellent', 'good', 'fair', 'poor'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instrument_rentals');
    }
};
