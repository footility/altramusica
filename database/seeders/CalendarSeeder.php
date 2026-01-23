<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\CalendarLesson;
use App\Models\CalendarSuspension;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== Importazione Calendario ===');
        
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $this->command->error("Nessun anno accademico attivo trovato");
            return;
        }
        
        $filePath = base_path('docs/materiale cliente/Calendario 2025-26.ods');
        
        if (!file_exists($filePath)) {
            $this->command->warn("File non trovato: {$filePath}");
            return;
        }
        
        try {
            $spreadsheet = IOFactory::load($filePath);
            
            // Analizza i fogli disponibili
            $sheetCount = $spreadsheet->getSheetCount();
            $this->command->info("Fogli trovati: {$sheetCount}");
            
            $importedLessons = 0;
            $importedSuspensions = 0;
            
            // Fase 1: fallback deterministico (il layout ODS è complesso e non sempre parsabile in modo affidabile).
            // Creiamo un calendario "operativo" standard: lun-ven attivi in tutto l'intervallo dell'anno,
            // poi sospensioni vuote (da integrare con parsing più fine quando necessario).
            $start = Carbon::parse($academicYear->start_date)->startOfDay();
            $end = Carbon::parse($academicYear->end_date)->startOfDay();

            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                $dow = strtolower($d->format('l')); // monday..sunday
                $isLessonDay = in_array($dow, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], true);

                CalendarLesson::updateOrCreate(
                    [
                        'academic_year_id' => $academicYear->id,
                        'date' => $d->format('Y-m-d'),
                    ],
                    [
                        'day_of_week' => $dow,
                        'is_active' => $isLessonDay,
                        'notes' => $isLessonDay ? null : 'Weekend',
                    ]
                );
                $importedLessons++;
            }
            
            $this->command->info("✓ Importate {$importedLessons} righe calendario (fallback)");
            $this->command->info("✓ Importate {$importedSuspensions} sospensioni");
            
        } catch (\Exception $e) {
            $this->command->error("Errore: " . $e->getMessage());
        }
    }
}
