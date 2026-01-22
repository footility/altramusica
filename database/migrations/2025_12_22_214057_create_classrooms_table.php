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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('capacity')->default(1);
            $table->json('equipment')->nullable();
            $table->boolean('available')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Add FK on course_offerings.classroom_id (table created earlier)
        if (Schema::hasTable('course_offerings') && Schema::hasColumn('course_offerings', 'classroom_id')) {
            Schema::table('course_offerings', function (Blueprint $table) {
                $table->foreign('classroom_id')->references('id')->on('classrooms')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('course_offerings') && Schema::hasColumn('course_offerings', 'classroom_id')) {
            // best-effort: drop FK if exists
            try {
                Schema::table('course_offerings', function (Blueprint $table) {
                    $table->dropForeign(['classroom_id']);
                });
            } catch (\Throwable $e) {
                // ignore
            }
        }
        Schema::dropIfExists('classrooms');
    }
};
