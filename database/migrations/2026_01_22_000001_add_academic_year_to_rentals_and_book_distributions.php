<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instrument_rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('instrument_rentals', 'academic_year_id')) {
                $table->foreignId('academic_year_id')->nullable()->after('instrument_id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('book_distributions', function (Blueprint $table) {
            if (!Schema::hasColumn('book_distributions', 'academic_year_id')) {
                $table->foreignId('academic_year_id')->nullable()->after('book_id')->constrained()->nullOnDelete();
            }
        });

        // Backfill from the current student-year association (Phase 1 compatibility)
        if (Schema::hasColumn('students', 'academic_year_id')) {
            DB::table('instrument_rentals')
                ->whereNull('instrument_rentals.academic_year_id')
                ->join('students', 'instrument_rentals.student_id', '=', 'students.id')
                ->update(['instrument_rentals.academic_year_id' => DB::raw('students.academic_year_id')]);

            DB::table('book_distributions')
                ->whereNull('book_distributions.academic_year_id')
                ->join('students', 'book_distributions.student_id', '=', 'students.id')
                ->update(['book_distributions.academic_year_id' => DB::raw('students.academic_year_id')]);
        }
    }

    public function down(): void
    {
        Schema::table('book_distributions', function (Blueprint $table) {
            if (Schema::hasColumn('book_distributions', 'academic_year_id')) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            }
        });

        Schema::table('instrument_rentals', function (Blueprint $table) {
            if (Schema::hasColumn('instrument_rentals', 'academic_year_id')) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            }
        });
    }
};

