<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected function dropAcademicYearForeignKeyIfExists(string $table): void
    {
        $row = DB::selectOne(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = 'academic_year_id'
               AND REFERENCED_TABLE_NAME = 'academic_years'
             LIMIT 1",
            [$table]
        );

        if ($row && !empty($row->CONSTRAINT_NAME)) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$row->CONSTRAINT_NAME}`");
        }
    }

    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // es. "2025-2026"
            $table->string('slug')->unique(); // es. "2025-26"
            $table->date('start_date'); // 1 settembre
            $table->date('end_date'); // 31 agosto
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Aggiungi foreign key a tutte le tabelle che referenziano l'anno scolastico
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        if (Schema::hasTable('course_offerings') && Schema::hasColumn('course_offerings', 'academic_year_id')) {
            Schema::table('course_offerings', function (Blueprint $table) {
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();
            });
        }

        Schema::table('contracts', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Add FK constraints for year-scoped tables created earlier (T0 column exists already)
        if (Schema::hasTable('instrument_rentals') && Schema::hasColumn('instrument_rentals', 'academic_year_id')) {
            Schema::table('instrument_rentals', function (Blueprint $table) {
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();
            });
        }

        if (Schema::hasTable('book_distributions') && Schema::hasColumn('book_distributions', 'academic_year_id')) {
            Schema::table('book_distributions', function (Blueprint $table) {
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        // NOTE (Fase 1 / migrate:refresh): other tables may reference academic_years via FK.
        // This migration must drop those FKs too because rollback order may drop academic_years
        // before the older create-table migrations are rolled back.

        if (Schema::hasTable('book_distributions') && Schema::hasColumn('book_distributions', 'academic_year_id')) {
            $this->dropAcademicYearForeignKeyIfExists('book_distributions');
            Schema::table('book_distributions', function (Blueprint $table) {
                $table->dropColumn('academic_year_id');
            });
        }

        if (Schema::hasTable('instrument_rentals') && Schema::hasColumn('instrument_rentals', 'academic_year_id')) {
            $this->dropAcademicYearForeignKeyIfExists('instrument_rentals');
            Schema::table('instrument_rentals', function (Blueprint $table) {
                $table->dropColumn('academic_year_id');
            });
        }

        if (Schema::hasTable('invoices') && Schema::hasColumn('invoices', 'academic_year_id')) {
            $this->dropAcademicYearForeignKeyIfExists('invoices');
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('academic_year_id');
            });
        }

        if (Schema::hasTable('contracts') && Schema::hasColumn('contracts', 'academic_year_id')) {
            $this->dropAcademicYearForeignKeyIfExists('contracts');
            Schema::table('contracts', function (Blueprint $table) {
                $table->dropColumn('academic_year_id');
            });
        }

        if (Schema::hasTable('enrollments') && Schema::hasColumn('enrollments', 'academic_year_id')) {
            $this->dropAcademicYearForeignKeyIfExists('enrollments');
            Schema::table('enrollments', function (Blueprint $table) {
                $table->dropColumn('academic_year_id');
            });
        }

        if (Schema::hasTable('course_offerings') && Schema::hasColumn('course_offerings', 'academic_year_id')) {
            $this->dropAcademicYearForeignKeyIfExists('course_offerings');
            Schema::table('course_offerings', function (Blueprint $table) {
                $table->dropColumn('academic_year_id');
            });
        }

        if (Schema::hasTable('students') && Schema::hasColumn('students', 'academic_year_id')) {
            $this->dropAcademicYearForeignKeyIfExists('students');
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('academic_year_id');
            });
        }

        Schema::dropIfExists('academic_years');
    }
};
