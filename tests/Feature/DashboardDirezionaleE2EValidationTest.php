<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\CourseType;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentYear;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * R11 · Controllo finale — Validazione dashboard direzionale + bilancio sociale (attività #8550).
 *
 * CONTESTO: R11 (#8531, doc 37) è SOLO DESIGN. Il §10 "Impatti tecnici" dichiara
 * esplicitamente che l'implementazione NON è parte di R11. Non esistono
 * `Admin\DirectorDashboardController`, `DirectorMetricsService`, la tabella opzionale
 * `expenses` (spese generali) né `historical_year_stats` (storico pre-migrazione ODS),
 * e nessuna rotta/voce di menu "Direzione ▸ Cruscotto" (in routes/web.php c'è solo la
 * dashboard OPERATIVA `admin.dashboard`). Quindi un E2E su controller/UI direzionale
 * NON è eseguibile (= "blocco" previsto dalla richiesta).
 *
 * Questo test fa due cose oneste:
 *   1) VALIDA I FLUSSI del design a livello dati+framework, sul substrato GIÀ esistente
 *      (`StudentYear`, `Payment`, `Invoice`, `AcademicYear`, `teacher_hours` dormiente):
 *      KPI iscritti + delta anno-su-anno; RETENTION cohort N-1→N (rimasti/ritirati/nuovi
 *      con somma che torna + breakdown per corso); SERIE STORICA multi-anno; FLUSSO DI
 *      CASSA mensile entrate (Payment) vs uscite PARZIALI (solo compensi docenti);
 *      maturato vs incassato (credito residuo); EXPORT bilancio sociale (CSV aggregato,
 *      solo conteggi/GDPR, caveat "uscite parziali" nel file, nome file parlante).
 *   2) FOTOGRAFA IL BLOCCO: assert che controller/service/tabelle opzionali/rotte del
 *      §10 sono ASSENTI — registro vivo del gap da colmare in implementazione.
 *
 * Trascrizioni: parte 2 r.42-54 — parte 1 r.116-122, 454-474.
 */
class DashboardDirezionaleE2EValidationTest extends TestCase
{
    use RefreshDatabase;

    private AcademicYear $y2324;
    private AcademicYear $y2425;
    private AcademicYear $y2526;

    protected function setUp(): void
    {
        parent::setUp();

        // L'asse del tempo del confronto direzionale (doc §2/§4): tre anni a sistema.
        $this->y2324 = $this->makeYear('2023/24', '2023-09-01', '2024-06-30', false);
        $this->y2425 = $this->makeYear('2024/25', '2024-09-01', '2025-06-30', false);
        $this->y2526 = $this->makeYear('2025/26', '2025-09-01', '2026-06-30', true);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper di scena (SOLO model esistenti; teacher_hours via DB facade)
    // ─────────────────────────────────────────────────────────────────────────

    private function makeYear(string $name, string $start, string $end, bool $active): AcademicYear
    {
        return AcademicYear::create([
            'name' => $name,
            'slug' => str_replace('/', '-', $name),
            'start_date' => $start,
            'end_date' => $end,
            'is_active' => $active,
        ]);
    }

    private function makeStudent(string $first, string $last): Student
    {
        return Student::create([
            'first_name' => $first, 'last_name' => $last, 'birth_date' => '2012-01-01',
        ]);
    }

    private function studentYear(Student $s, AcademicYear $y, string $status, ?string $withdrawnAt = null): StudentYear
    {
        return StudentYear::create([
            'student_id' => $s->id,
            'academic_year_id' => $y->id,
            'status' => $status,
            'privacy_consent' => true,
            'withdrawn_at' => $withdrawnAt,
        ]);
    }

    private function offering(string $courseCode, string $typeName): CourseOffering
    {
        $code = substr($courseCode, 0, 2);
        $type = CourseType::firstOrCreate(['code' => $code], ['name' => $typeName, 'duration_minutes' => 30]);
        $course = Course::firstOrCreate(['code' => $courseCode], ['course_type_id' => $type->id, 'name' => $typeName]);

        return CourseOffering::firstOrCreate(
            ['course_id' => $course->id, 'academic_year_id' => $this->y2526->id],
            ['status' => 'active'],
        );
    }

    private function enroll(Student $s, CourseOffering $o, AcademicYear $y): Enrollment
    {
        return Enrollment::create([
            'academic_year_id' => $y->id,
            'student_id' => $s->id,
            'course_offering_id' => $o->id,
            'enrollment_date' => $y->start_date->format('Y-m-d'),
            'start_date' => $y->start_date->format('Y-m-d'),
            'status' => 'active',
        ]);
    }

    /** Crea una fattura per studente/anno e (opzionale) un incasso reale a quella data. */
    private function invoiceWithPayment(Student $s, AcademicYear $y, float $total, ?float $paid = null, ?string $paymentDate = null): Invoice
    {
        $inv = Invoice::create([
            'academic_year_id' => $y->id,
            'student_id' => $s->id,
            'invoice_number' => 'INV-' . $y->slug . '-' . $s->id . '-' . DB::table('invoices')->count(),
            'invoice_date' => $y->start_date->format('Y-m-d'),
            'due_date' => $y->start_date->format('Y-m-d'),
            'subtotal' => $total,
            'total_amount' => $total,
            'status' => 'sent',
        ]);

        if ($paid !== null) {
            Payment::create([
                'invoice_id' => $inv->id,
                'payment_date' => $paymentDate ?? $y->start_date->format('Y-m-d'),
                'amount' => $paid,
                'payment_method' => 'bank_transfer',
            ]);
        }

        return $inv;
    }

    /** Registra un consuntivo compensi docenti nella tabella dormiente teacher_hours (R8). */
    private function teacherHours(AcademicYear $y, float $total, string $periodStart): void
    {
        $teacherId = DB::table('teachers')->insertGetId([
            'first_name' => 'Doc', 'last_name' => 'Ente',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('teacher_hours')->insert([
            'teacher_id' => $teacherId,
            'academic_year_id' => $y->id,
            'period_start' => $periodStart,
            'period_end' => $periodStart,
            'total_amount' => $total,
            'status' => 'approved',
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // MOTORE METRICHE DIREZIONALI (doc §10 DirectorMetricsService) — sola lettura,
    // esercitato a livello dati su entità esistenti. Specchio onesto del design.
    // ─────────────────────────────────────────────────────────────────────────

    /** doc §3/§10: iscritti = StudentYear con status=enrolled per anno. */
    private function enrollmentsByYear(AcademicYear $y): int
    {
        return StudentYear::where('academic_year_id', $y->id)->where('status', 'enrolled')->count();
    }

    /** doc §5/§10: cohort retention N-1 → N (rimasti / ritirati / nuovi). */
    private function retention(AcademicYear $prev, AcademicYear $curr): array
    {
        $cohort = StudentYear::where('academic_year_id', $prev->id)
            ->where('status', 'enrolled')->pluck('student_id');
        $currentEnrolled = StudentYear::where('academic_year_id', $curr->id)
            ->where('status', 'enrolled')->pluck('student_id');

        $remained = $cohort->intersect($currentEnrolled);          // c'era N-1 e c'è N
        $withdrawn = $cohort->diff($currentEnrolled);              // c'era N-1 e non c'è N
        $new = $currentEnrolled->diff($cohort);                    // c'è N ma non era N-1

        return [
            'cohort' => $cohort->count(),
            'remained' => $remained->count(),
            'withdrawn' => $withdrawn->count(),
            'new' => $new->count(),
            'current_total' => $currentEnrolled->count(),
            'rate' => $cohort->count() > 0
                ? round($remained->count() / $cohort->count() * 100, 1)
                : null, // coorte vuota → non calcolabile (doc §5)
        ];
    }

    /** doc §6/§10: flusso di cassa mensile — entrate (Payment via Invoice) + uscite PARZIALI. */
    private function cashFlow(AcademicYear $y): array
    {
        $invoiceIds = Invoice::where('academic_year_id', $y->id)->pluck('id');

        $byMonth = Payment::whereIn('invoice_id', $invoiceIds)->get()
            ->groupBy(fn ($p) => Carbon::parse($p->payment_date)->format('Y-m'))
            ->map(fn ($g) => (float) $g->sum('amount'));

        // uscite = SOLO compensi docenti noti (teacher_hours dormiente). Parziali per design.
        $uscite = (float) DB::table('teacher_hours')->where('academic_year_id', $y->id)->sum('total_amount');
        $entrate = (float) $byMonth->sum();

        return [
            'by_month' => $byMonth->toArray(),
            'entrate' => $entrate,
            'uscite' => $uscite,
            'saldo' => $entrate - $uscite,
            'uscite_parziali' => true, // badge sempre presente finché manca tabella `expenses` (§0.5/§6)
        ];
    }

    /** doc §6/§10: maturato (Invoice) vs incassato (Payment) → credito residuo. */
    private function accruedVsCollected(AcademicYear $y): array
    {
        $invoiceIds = Invoice::where('academic_year_id', $y->id)->pluck('id');
        $maturato = (float) Invoice::where('academic_year_id', $y->id)->sum('total_amount');
        $incassato = (float) Payment::whereIn('invoice_id', $invoiceIds)->sum('amount');

        return ['maturato' => $maturato, 'incassato' => $incassato, 'credito_residuo' => $maturato - $incassato];
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 1. SUBSTRATO — i dati grezzi del cruscotto esistono già (deve passare)
    // ═════════════════════════════════════════════════════════════════════════

    /** doc §1: il dato grezzo c'è quasi tutto — tabelle iscritti/incassi/anni presenti. */
    public function test_substrato_tabelle_direzionali_presenti(): void
    {
        $this->assertTrue(Schema::hasTable('student_years'));
        $this->assertTrue(Schema::hasTable('academic_years'));
        $this->assertTrue(Schema::hasTable('payments'));
        $this->assertTrue(Schema::hasTable('invoices'));
        // status enum di student_years copre gli stati del design (enrolled/withdrawn)
        $this->assertContains('enrolled', ['prospect', 'interested', 'enrolled', 'withdrawn']);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 2. KPI ISCRITTI + DELTA ANNO-SU-ANNO (doc §3)
    // ═════════════════════════════════════════════════════════════════════════

    /** doc §3: card "Iscritti" = StudentYear enrolled; gli interested/prospect non contano. */
    public function test_kpi_iscritti_conta_solo_enrolled(): void
    {
        $this->studentYear($this->makeStudent('A', 'A'), $this->y2526, 'enrolled');
        $this->studentYear($this->makeStudent('B', 'B'), $this->y2526, 'enrolled');
        $this->studentYear($this->makeStudent('C', 'C'), $this->y2526, 'interested'); // fuori
        $this->studentYear($this->makeStudent('D', 'D'), $this->y2526, 'prospect');   // fuori

        $this->assertSame(2, $this->enrollmentsByYear($this->y2526));
    }

    /** doc §0.2/§3: ogni KPI ha il delta vs anno precedente (valore assoluto + %). */
    public function test_kpi_delta_anno_su_anno(): void
    {
        // 2024/25: 4 iscritti — 2025/26: 5 iscritti → +1 (+25%)
        for ($i = 0; $i < 4; $i++) {
            $this->studentYear($this->makeStudent("P$i", 'X'), $this->y2425, 'enrolled');
        }
        for ($i = 0; $i < 5; $i++) {
            $this->studentYear($this->makeStudent("Q$i", 'Y'), $this->y2526, 'enrolled');
        }

        $curr = $this->enrollmentsByYear($this->y2526);
        $prev = $this->enrollmentsByYear($this->y2425);
        $deltaAbs = $curr - $prev;
        $deltaPct = round($deltaAbs / $prev * 100, 1);

        $this->assertSame(5, $curr);
        $this->assertSame(1, $deltaAbs);
        $this->assertSame(25.0, $deltaPct);
    }

    /** doc §3/§8: primo anno a sistema (nessun precedente) → delta non calcolabile, non un crash. */
    public function test_kpi_delta_assente_se_nessun_anno_precedente(): void
    {
        $primo = $this->makeYear('2008/09', '2008-09-01', '2009-06-30', false);
        $this->studentYear($this->makeStudent('Solo', 'Uno'), $primo, 'enrolled');

        // nessun anno antecedente al 2008/09 a sistema → delta = null ("—")
        $precedente = AcademicYear::where('start_date', '<', $primo->start_date)->orderByDesc('start_date')->first();
        $this->assertNull($precedente);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 3. RETENTION COHORT (doc §5) — la metrica delicata
    // ═════════════════════════════════════════════════════════════════════════

    /** doc §5: rimasti + nuovi = iscritti anno corrente (la somma DEVE tornare). */
    public function test_retention_cohort_somma_torna(): void
    {
        // Coorte 2024/25: 4 iscritti (R1..R4). 2025/26: R1,R2,R3 restano + N1,N2 nuovi = 5.
        $r1 = $this->makeStudent('R1', 'X');
        $r2 = $this->makeStudent('R2', 'X');
        $r3 = $this->makeStudent('R3', 'X');
        $r4 = $this->makeStudent('R4', 'X'); // non rinnova
        foreach ([$r1, $r2, $r3, $r4] as $s) {
            $this->studentYear($s, $this->y2425, 'enrolled');
        }
        foreach ([$r1, $r2, $r3] as $s) {
            $this->studentYear($s, $this->y2526, 'enrolled');
        }
        $this->studentYear($this->makeStudent('N1', 'Y'), $this->y2526, 'enrolled');
        $this->studentYear($this->makeStudent('N2', 'Y'), $this->y2526, 'enrolled');

        $ret = $this->retention($this->y2425, $this->y2526);

        $this->assertSame(4, $ret['cohort']);
        $this->assertSame(3, $ret['remained']);
        $this->assertSame(1, $ret['withdrawn']);
        $this->assertSame(2, $ret['new']);
        $this->assertSame(5, $ret['current_total']);
        // auto-verifica del design: rimasti + nuovi = totale anno corrente
        $this->assertSame($ret['current_total'], $ret['remained'] + $ret['new']);
        $this->assertSame(75.0, $ret['rate']); // 3/4
    }

    /** doc §5: ritirato in corso d'anno (withdrawn_at) NON è "rimasto". */
    public function test_retention_ritirato_in_corso_non_e_rimasto(): void
    {
        $a = $this->makeStudent('A', 'A');
        $this->studentYear($a, $this->y2425, 'enrolled');
        // in 2025/26 risulta withdrawn (ritirato in corso) → fuori dai rimasti
        $this->studentYear($a, $this->y2526, 'withdrawn', withdrawnAt: '2025-11-01');

        $ret = $this->retention($this->y2425, $this->y2526);
        $this->assertSame(1, $ret['cohort']);
        $this->assertSame(0, $ret['remained']);
        $this->assertSame(1, $ret['withdrawn']);
        $this->assertSame(0.0, $ret['rate']);
    }

    /** doc §5/§8: coorte vuota (nessun anno precedente con iscritti) → rate non calcolabile (null). */
    public function test_retention_coorte_vuota_non_calcolabile(): void
    {
        $this->studentYear($this->makeStudent('Solo', 'N'), $this->y2526, 'enrolled');
        $ret = $this->retention($this->y2425, $this->y2526); // 2024/25 vuoto
        $this->assertSame(0, $ret['cohort']);
        $this->assertNull($ret['rate']);
    }

    /** doc §5: breakdown retention per corso/strumento (dove si perde di più). */
    public function test_retention_breakdown_per_corso(): void
    {
        $pf = $this->offering('PF1', 'Pianoforte');
        $ch = $this->offering('CH1', 'Chitarra');

        // Pianoforte: 2 in coorte, 2 restano = 100%
        $p1 = $this->makeStudent('P1', 'X');
        $p2 = $this->makeStudent('P2', 'X');
        // Chitarra: 2 in coorte, 1 resta = 50%
        $c1 = $this->makeStudent('C1', 'Y');
        $c2 = $this->makeStudent('C2', 'Y');

        foreach ([$p1, $p2, $c1, $c2] as $s) {
            $this->studentYear($s, $this->y2425, 'enrolled');
        }
        $this->enroll($p1, $pf, $this->y2526);
        $this->enroll($p2, $pf, $this->y2526);
        $this->enroll($c1, $ch, $this->y2526);
        foreach ([$p1, $p2, $c1] as $s) {
            $this->studentYear($s, $this->y2526, 'enrolled'); // c2 non rinnova
        }

        // retention per corso: studenti iscritti N e legati a quel corso, intersecati con coorte N-1
        $cohort = StudentYear::where('academic_year_id', $this->y2425->id)->where('status', 'enrolled')->pluck('student_id');
        $byCourse = [];
        foreach (['PF1' => $pf, 'CH1' => $ch] as $label => $off) {
            $courseStudents = Enrollment::where('course_offering_id', $off->id)->pluck('student_id');
            $remained = $cohort->intersect($courseStudents)->count();
            $byCourse[$label] = $remained;
        }

        $this->assertSame(2, $byCourse['PF1']); // pianoforte: entrambi rimasti
        $this->assertSame(1, $byCourse['CH1']); // chitarra: solo uno
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 4. SERIE STORICA MULTI-ANNO (doc §4)
    // ═════════════════════════════════════════════════════════════════════════

    /** doc §4: serie iscritti per anno scolastico, ordinata sull'asse del tempo. */
    public function test_serie_storica_iscritti_per_anno(): void
    {
        // 2023/24: 2 · 2024/25: 4 · 2025/26: 5
        $this->seedEnrolled($this->y2324, 2);
        $this->seedEnrolled($this->y2425, 4);
        $this->seedEnrolled($this->y2526, 5);

        $serie = AcademicYear::orderBy('start_date')->get()
            ->mapWithKeys(fn ($y) => [$y->name => $this->enrollmentsByYear($y)]);

        $this->assertSame([
            '2023/24' => 2,
            '2024/25' => 4,
            '2025/26' => 5,
        ], $serie->toArray());

        // media di riferimento (doc §4 "media ultimi N anni")
        $this->assertEqualsWithDelta(3.67, round($serie->avg(), 2), 0.01);
    }

    private function seedEnrolled(AcademicYear $y, int $n): void
    {
        for ($i = 0; $i < $n; $i++) {
            $this->studentYear($this->makeStudent("S{$y->id}_$i", 'Z'), $y, 'enrolled');
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 5. FLUSSO DI CASSA — entrate vs uscite PARZIALI (doc §6)
    // ═════════════════════════════════════════════════════════════════════════

    /** doc §6: entrate = incassi reali (Payment) aggregati per mese di payment_date. */
    public function test_cashflow_entrate_per_mese(): void
    {
        $s = $this->makeStudent('Pag', 'Atore');
        $this->invoiceWithPayment($s, $this->y2526, 200, 100, '2025-09-15');
        $this->invoiceWithPayment($s, $this->y2526, 200, 80, '2025-10-10');
        $this->invoiceWithPayment($s, $this->y2526, 200, 50, '2025-10-20'); // stesso mese → somma

        $cf = $this->cashFlow($this->y2526);

        $this->assertSame(100.0, $cf['by_month']['2025-09']);
        $this->assertSame(130.0, $cf['by_month']['2025-10']); // 80 + 50
        $this->assertSame(230.0, $cf['entrate']);
    }

    /** doc §0.5/§6: uscite = SOLO compensi docenti (teacher_hours), badge "parziale" sempre vero. */
    public function test_cashflow_uscite_solo_compensi_e_badge_parziale(): void
    {
        $s = $this->makeStudent('Pag', 'Atore');
        $this->invoiceWithPayment($s, $this->y2526, 1000, 1000, '2025-09-15');
        $this->teacherHours($this->y2526, 600, '2025-09-30');

        $cf = $this->cashFlow($this->y2526);

        $this->assertSame(1000.0, $cf['entrate']);
        $this->assertSame(600.0, $cf['uscite']);
        $this->assertSame(400.0, $cf['saldo']);
        // il badge "uscite parziali" è SEMPRE presente finché manca la tabella expenses (§10)
        $this->assertTrue($cf['uscite_parziali']);
    }

    /** doc §8: motore compensi R8 non attivo → uscite = 0, saldo = solo entrate (dichiarato). */
    public function test_cashflow_senza_compensi_uscite_zero(): void
    {
        $s = $this->makeStudent('Pag', 'Atore');
        $this->invoiceWithPayment($s, $this->y2526, 500, 500, '2025-09-15');

        $cf = $this->cashFlow($this->y2526);
        $this->assertSame(0.0, $cf['uscite']);
        $this->assertSame(500.0, $cf['saldo']);
        $this->assertTrue($cf['uscite_parziali']);
    }

    /** doc §6: maturato (Invoice) vs incassato (Payment) → credito ancora da incassare. */
    public function test_maturato_vs_incassato_credito_residuo(): void
    {
        $s = $this->makeStudent('Cre', 'Dito');
        $this->invoiceWithPayment($s, $this->y2526, 300, 200, '2025-09-15'); // 100 residuo
        $this->invoiceWithPayment($s, $this->y2526, 150, 150, '2025-10-15'); // saldata

        $a = $this->accruedVsCollected($this->y2526);
        $this->assertSame(450.0, $a['maturato']);
        $this->assertSame(350.0, $a['incassato']);
        $this->assertSame(100.0, $a['credito_residuo']);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 6. EXPORT BILANCIO SOCIALE (doc §7) — CSV aggregato, GDPR, caveat nel file
    // ═════════════════════════════════════════════════════════════════════════

    /**
     * doc §7/§10: export CSV via stream nativo (fputcsv, nessun package nuovo).
     * Solo AGGREGATI (mai nomi allievi — GDPR), caveat "uscite parziali" in testa al file,
     * nome file parlante con anno + perimetro.
     */
    public function test_export_bilancio_sociale_csv_aggregato_e_caveat(): void
    {
        $mario = $this->makeStudent('Mario', 'Rossi'); // PII che NON deve finire nell'export
        $this->studentYear($mario, $this->y2425, 'enrolled');
        $this->studentYear($mario, $this->y2526, 'enrolled');
        $this->invoiceWithPayment($mario, $this->y2526, 300, 250, '2025-09-15');
        $this->teacherHours($this->y2526, 100, '2025-09-30');

        $iscritti = $this->enrollmentsByYear($this->y2526);
        $ret = $this->retention($this->y2425, $this->y2526);
        $cf = $this->cashFlow($this->y2526);
        $acc = $this->accruedVsCollected($this->y2526);

        // export = stream CSV nativo (StreamedResponse equivalente)
        $fh = fopen('php://temp', 'r+');
        fputcsv($fh, ['L\'Altra Musica · Bilancio sociale — AA 2025/26']);
        fputcsv($fh, ['NB: uscite parziali — solo compensi docenti (no affitti/utenze/SIAE/altro)']);
        fputcsv($fh, ['Metrica', 'Valore']);
        fputcsv($fh, ['Iscritti', $iscritti]);
        fputcsv($fh, ['Retention %', $ret['rate']]);
        fputcsv($fh, ['Rimasti / Coorte', $ret['remained'] . ' / ' . $ret['cohort']]);
        fputcsv($fh, ['Nuovi ingressi', $ret['new']]);
        fputcsv($fh, ['Entrate (incassato)', $cf['entrate']]);
        fputcsv($fh, ['Uscite (solo compensi)', $cf['uscite']]);
        fputcsv($fh, ['Saldo di cassa', $cf['saldo']]);
        fputcsv($fh, ['Credito da incassare', $acc['credito_residuo']]);
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        // 1) i numeri aggregati ci sono (fputcsv può quotare i campi con spazi → regex tollerante)
        $this->assertStringContainsString('Iscritti,1', $csv);
        $this->assertMatchesRegularExpression('/Saldo di cassa"?,150/', $csv); // 250 - 100
        $this->assertMatchesRegularExpression('/Credito da incassare"?,50/', $csv); // 300 - 250
        // 2) il caveat viaggia col file (doc §7)
        $this->assertStringContainsString('uscite parziali', $csv);
        $this->assertStringContainsString('Bilancio sociale', $csv);
        // 3) GDPR: nessuna PII (nome allievo) nell'export aggregato
        $this->assertStringNotContainsString('Mario', $csv);
        $this->assertStringNotContainsString('Rossi', $csv);
    }

    /** doc §7: nome file parlante (anno + perimetro), non export(3).csv ambiguo. */
    public function test_export_nome_file_parlante(): void
    {
        $filename = 'bilancio-sociale_' . $this->y2526->slug . '_altramusica.csv';
        $this->assertSame('bilancio-sociale_2025-26_altramusica.csv', $filename);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // 7. BLOCCO — fotografia del gap di implementazione (doc §10, NON parte di R11)
    // ═════════════════════════════════════════════════════════════════════════

    /** §10: il controller direzionale dedicato (separato dall'operativo) NON esiste. */
    public function test_blocco_director_dashboard_controller_assente(): void
    {
        $this->assertFalse(
            class_exists(\App\Http\Controllers\Admin\DirectorDashboardController::class),
            'DirectorDashboardController da implementare (vista Direzione, sola lettura)',
        );
        // la dashboard OPERATIVA invece esiste già (substrato, non va duplicata)
        $this->assertTrue(class_exists(\App\Http\Controllers\Admin\DashboardController::class));
    }

    /** §10: il servizio di aggregazione cache-abile NON esiste ancora. */
    public function test_blocco_director_metrics_service_assente(): void
    {
        $this->assertFalse(
            class_exists(\App\Services\DirectorMetricsService::class),
            'DirectorMetricsService (enrollmentsByYear/retention/cashFlow/accruedVsCollected) da implementare',
        );
    }

    /** §10: la tabella OPZIONALE `expenses` (spese generali) per chiudere il conto economico NON esiste → uscite restano parziali. */
    public function test_blocco_tabella_expenses_assente(): void
    {
        $this->assertFalse(
            Schema::hasTable('expenses'),
            'Tabella spese generali assente → badge "uscite parziali" obbligatorio (§0.5/§6)',
        );
    }

    /** §10: la tabella OPZIONALE `historical_year_stats` (storico pre-migrazione ODS) NON esiste → serie parte dal primo anno a sistema. */
    public function test_blocco_tabella_historical_year_stats_assente(): void
    {
        $this->assertFalse(
            Schema::hasTable('historical_year_stats'),
            'Storico pre-2024 (foglio grafico ODS) da importare — serie §4 parziale finché assente',
        );
    }

    /** §10: nessuna rotta/voce di menu "Direzione ▸ Cruscotto" (esiste solo admin.dashboard operativa). */
    public function test_blocco_rotte_direzione_assenti(): void
    {
        $this->assertFalse(Route::has('admin.director.dashboard'));
        $this->assertFalse(Route::has('admin.director.export'));
        $this->assertFalse(Route::has('admin.bilancio-sociale.export'));
        // la rotta operativa c'è (substrato)
        $this->assertTrue(Route::has('admin.dashboard'));
    }
}
