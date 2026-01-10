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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('exam_type', ['ABRSM', 'LCM', 'other']);
            $table->integer('level')->nullable();
            $table->enum('subject', ['instrument', 'theory', 'both'])->default('instrument');
            $table->date('exam_date')->nullable();
            $table->date('registration_date')->nullable();
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->enum('result', ['passed', 'failed', 'merit', 'distinction', 'pending'])->nullable();
            $table->string('grade')->nullable();
            $table->string('certificate_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
