<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creazione anno accademico...');
        
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2025-26'],
            [
                'slug' => '2025-26',
                'start_date' => '2025-09-01',
                'end_date' => '2026-08-31',
                'is_active' => true,
            ]
        );
        
        // Disattiva altri anni
        AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        
        $this->command->info("✓ Anno accademico: {$academicYear->name} (attivo)");
    }
}
