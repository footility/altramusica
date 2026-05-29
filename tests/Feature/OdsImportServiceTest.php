<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Services\OdsImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class OdsImportServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeYear(): AcademicYear
    {
        return AcademicYear::create([
            'name' => '2025-26',
            'slug' => '2025-26',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);
    }

    /**
     * Crea un xlsx temporaneo con header in stile "Risposte del modulo" e righe sporche.
     */
    private function makeDirtyFile(): string
    {
        $rows = [
            ['Cognome allievo', 'Nome allievo', 'Codice fiscale allievo', 'Data di nascita allievo', 'E-mail allievo', 'Note prove e didattiche'],
            ['Rossi', 'Mario', 'RSSMRA10A01H501A', '2010-01-01', 'mario@example.com', 'ok'],           // pulita
            ['Bianchi', 'Luca', '', '2011-02-02', 'luca@example.com', ''],                              // CF mancante
            ['Verdi', 'Anna', 'ABC', '2012-03-03', 'No', ''],                                           // CF malformato + email invalida
            ['Neri', 'Sara', 'NREXXX10A41H501B', '2010-04-04', 'sara@example.com', 'tel 333 1234567'],  // note con dati
            ['Rossi', 'Mario', 'RSSMRA99A01H501Z', '2009-05-05', 'mario2@example.com', ''],             // omonimo di riga 2
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('dati');
        $sheet->fromArray($rows, null, 'A1');

        $path = storage_path('framework/testing/ods_dirty_' . uniqid() . '.xlsx');
        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }

    public function test_dry_run_reports_anomalies_without_writing(): void
    {
        $this->makeYear();
        $path = $this->makeDirtyFile();

        $report = (new OdsImportService())->importStudents($path, 'dati', null, true);

        $this->assertTrue($report['dry_run']);
        $this->assertSame(5, $report['total_rows']);
        // dry-run: niente scritture
        $this->assertSame(0, Student::count());
        $this->assertSame(5, $report['created']); // tutti "sarebbero" creati (DB vuoto)

        $this->assertCount(1, $report['anomalies']['missing_tax_code']);
        $this->assertCount(1, $report['anomalies']['invalid_tax_code']);
        $this->assertCount(1, $report['anomalies']['invalid_email']);
        $this->assertCount(1, $report['anomalies']['notes_with_data']);
        // Rossi Mario presente su 2 righe -> entrambe segnate omonime
        $this->assertCount(2, $report['anomalies']['homonyms']);

        @unlink($path);
    }

    public function test_real_import_persists_students(): void
    {
        $this->makeYear();
        $path = $this->makeDirtyFile();

        $report = (new OdsImportService())->importStudents($path, 'dati', null, false);

        $this->assertFalse($report['dry_run']);
        $this->assertGreaterThan(0, Student::count());
        $this->assertDatabaseHas('students', ['first_name' => 'Mario', 'last_name' => 'Rossi']);
        // record univoco persistito col CF normalizzato (maiuscolo, senza spazi)
        $this->assertNotNull(Student::where('tax_code', 'NREXXX10A41H501B')->first());
        // i due "Rossi Mario" omonimi collassano in un unico studente (match per nome+cognome)
        $this->assertSame(1, Student::where('first_name', 'Mario')->where('last_name', 'Rossi')->count());

        @unlink($path);
    }
}
