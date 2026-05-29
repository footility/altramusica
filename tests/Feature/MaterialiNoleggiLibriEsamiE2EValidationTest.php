<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\BookController;
use App\Models\AcademicYear;
use App\Models\Book;
use App\Models\BookDistribution;
use App\Models\Exam;
use App\Models\Instrument;
use App\Models\InstrumentRental;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * R6 · Controllo finale — Validazione materiali/noleggi/libri/esami (attività #8545).
 *
 * Test E2E che esercita SOLO ciò che esiste in codice (modelli Book, InstrumentRental,
 * Exam, BookDistribution + relativi CRUD admin) e documenta esplicitamente i gap
 * rispetto al design R6. Riferimenti: docs/28_UX_MATERIALI_NOLEGGI_LIBRI_ESAMI.md
 * §1 (gap rilevati), §4 (cauzione come stato), §5 (azioni rapide), §6 (catalogo libri).
 *
 * Trascrizioni parte 2 r.76-90 (wireframe noleggi+cauzione), r.142 (+Noleggio),
 * r.178-180 (catalogo libri editabile).
 */
class MaterialiNoleggiLibriEsamiE2EValidationTest extends TestCase
{
    use RefreshDatabase;

    private AcademicYear $currentYear;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('acl:sync', ['--reset-defaults' => true]);

        $this->currentYear = AcademicYear::create([
            'name' => '2025/26',
            'slug' => '2025-26',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');
    }

    private function makeStudent(string $first, string $last): Student
    {
        return Student::create([
            'first_name' => $first,
            'last_name' => $last,
            'birth_date' => '2012-01-01',
        ]);
    }

    private function makeInstrument(string $type = 'Violino'): Instrument
    {
        return Instrument::create([
            'type' => $type,
            'brand' => 'Stentor',
            'size' => '1/4',
            'status' => 'available',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CIÒ CHE FUNZIONA (deve passare esercitando il codice reale)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * FLUSSO §6 — Catalogo libri editabile: rinomina un libro mock (placeholder)
     * con etichette pulite (titolo/autore/editore/prezzo).
     */
    public function test_catalogo_libri_rinomina_libro_mock(): void
    {
        // libro "mock" con titolo placeholder, come quelli da ripulire (design §6)
        $book = Book::create([
            'title' => 'Libro 1',
            'price' => 0,
            'stock_quantity' => 0,
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.books.update', $book), [
            'title' => 'Piano Adventures · Level 1',
            'author' => 'Nancy & Randall Faber',
            'publisher' => 'Hal Leonard',
            'isbn' => '9781616770?',
            'price' => 18.90,
            'stock_quantity' => 12,
        ]);

        $response->assertRedirect(route('admin.books.index'));
        $fresh = $book->fresh();
        $this->assertSame('Piano Adventures · Level 1', $fresh->title);
        $this->assertSame('Hal Leonard', $fresh->publisher);
        $this->assertSame('18.90', $fresh->price);
    }

    /** Validazione catalogo (design §6): titolo e prezzo obbligatori — niente record fittizi. */
    public function test_catalogo_libri_titolo_e_prezzo_obbligatori(): void
    {
        $this->actingAs($this->user)
            ->post(route('admin.books.store'), ['stock_quantity' => 1])
            ->assertSessionHasErrors(['title', 'price']);

        $this->assertSame(0, Book::count());
    }

    /**
     * FLUSSO §6 — Eliminazione protetta: un libro già distribuito non si cancella
     * (evita righe orfane nelle consegne).
     */
    public function test_catalogo_libri_eliminazione_protetta_se_distribuito(): void
    {
        $student = $this->makeStudent('Mario', 'Rossi');
        $book = Book::create(['title' => 'Teoria musicale di base', 'price' => 9.50, 'stock_quantity' => 5]);

        BookDistribution::create([
            'student_id' => $student->id,
            'book_id' => $book->id,
            'academic_year_id' => $this->currentYear->id,
            'distribution_date' => '2025-10-01',
            'quantity' => 1,
            'price_paid' => 9.50,
        ]);

        $this->actingAs($this->user)
            ->delete(route('admin.books.destroy', $book))
            ->assertRedirect(route('admin.books.index'));

        // Il libro distribuito NON deve essere cancellato (resta in tabella).
        $this->assertDatabaseHas('books', ['id' => $book->id]);
    }

    /**
     * FLUSSO §5b — Consegna libro pesca dal catalogo: il prezzo del catalogo è il
     * default, sovrascrivibile per-studente (price_paid).
     */
    public function test_consegna_libro_dal_catalogo_con_prezzo_override(): void
    {
        $student = $this->makeStudent('Mario', 'Rossi');
        $book = Book::create(['title' => 'Piano Adventures · Level 1', 'publisher' => 'Hal Leonard', 'price' => 18.90, 'stock_quantity' => 10]);

        $this->actingAs($this->user)->post(route('admin.book-distributions.store'), [
            'student_id' => $student->id,
            'book_id' => $book->id,
            'academic_year_id' => $this->currentYear->id,
            'distribution_date' => '2026-05-29',
            'quantity' => 1,
            'price_paid' => 15.00, // override per-studente rispetto al catalogo
        ]);

        $dist = BookDistribution::first();
        $this->assertNotNull($dist);
        $this->assertSame($book->id, $dist->book_id);
        $this->assertSame('15.00', $dist->price_paid, 'price_paid sovrascrive il prezzo di catalogo.');
        // L'etichetta pulita pesca dal catalogo (no titolo ridigitato a mano).
        $this->assertSame('Piano Adventures · Level 1', $dist->book->title);
    }

    /**
     * FLUSSO §5a — Registra noleggio con cauzione: InstrumentRental creato attivo,
     * deposit valorizzato, strumento marcato "rented".
     */
    public function test_registra_noleggio_con_cauzione(): void
    {
        $student = $this->makeStudent('Mario', 'Rossi');
        $instrument = $this->makeInstrument('Violino');

        $response = $this->actingAs($this->user)->post(route('admin.instrument-rentals.store'), [
            'student_id' => $student->id,
            'instrument_id' => $instrument->id,
            'academic_year_id' => $this->currentYear->id,
            'start_date' => '2025-10-01',
            'monthly_fee' => 25.00,
            'deposit' => 150.00,
            'status' => 'active',
        ]);

        $rental = InstrumentRental::first();
        $this->assertNotNull($rental);
        $response->assertRedirect(route('admin.instrument-rentals.show', $rental));
        $this->assertSame('150.00', $rental->deposit, 'La cauzione è registrata sul noleggio.');
        $this->assertSame('active', $rental->status);
        // Lo strumento passa a "rented" (coerente con la striscia "noleggi attivi", §3).
        $this->assertSame('rented', $instrument->fresh()->status);
    }

    /**
     * FLUSSO §4/§5a — Registra restituzione (approssimazione attuale): il noleggio
     * passa a "returned" con return_date/return_condition; lo strumento torna disponibile.
     */
    public function test_registra_restituzione_strumento(): void
    {
        $student = $this->makeStudent('Mario', 'Rossi');
        $instrument = $this->makeInstrument('Tromba');
        $rental = InstrumentRental::create([
            'student_id' => $student->id,
            'instrument_id' => $instrument->id,
            'academic_year_id' => $this->currentYear->id,
            'start_date' => '2024-10-01',
            'monthly_fee' => 20.00,
            'deposit' => 100.00,
            'status' => 'active',
        ]);
        $instrument->update(['status' => 'rented']);

        $this->actingAs($this->user)->put(route('admin.instrument-rentals.update', $rental), [
            'student_id' => $student->id,
            'instrument_id' => $instrument->id,
            'academic_year_id' => $this->currentYear->id,
            'start_date' => '2024-10-01',
            'monthly_fee' => 20.00,
            'deposit' => 100.00,
            'status' => 'returned',
            'return_date' => '2025-06-04',
            'return_condition' => 'good',
        ])->assertRedirect(route('admin.instrument-rentals.index'));

        $fresh = $rental->fresh();
        $this->assertSame('returned', $fresh->status);
        $this->assertSame('good', $fresh->return_condition);
        $this->assertNotNull($fresh->return_date);
        $this->assertSame('available', $instrument->fresh()->status);
    }

    /**
     * FLUSSO §5d — Registra esame: Exam creato per lo studente.
     * Usa valori validi sia per il controller sia per l'enum DB (vedi BUG sotto):
     * exam_type='other', subject='theory'.
     */
    public function test_registra_esame(): void
    {
        $student = $this->makeStudent('Mario', 'Rossi');

        $this->actingAs($this->user)->post(route('admin.exams.store'), [
            'student_id' => $student->id,
            'exam_type' => 'other',
            'level' => 3,
            'subject' => 'theory',
            'exam_date' => '2026-06-15',
            'registration_fee' => 95.00,
            'result' => 'pending',
        ])->assertRedirect(route('admin.exams.index'));

        $exam = Exam::first();
        $this->assertNotNull($exam);
        $this->assertSame($student->id, $exam->student_id);
        $this->assertSame('other', $exam->exam_type);
        $this->assertSame(3, $exam->level);
        $this->assertSame('pending', $exam->result, 'Esame futuro = "In calendario" (pending), §7.');
    }

    /**
     * Aggregazione per studente+anno: le relazioni esistenti permettono già di
     * raccogliere i 3 mondi presenti (noleggi/libri/esami) per uno studente.
     */
    public function test_relazioni_studente_aggregano_noleggi_libri_esami(): void
    {
        $student = $this->makeStudent('Mario', 'Rossi');
        $instrument = $this->makeInstrument();
        $book = Book::create(['title' => 'Solfeggio', 'price' => 7.00, 'stock_quantity' => 3]);

        InstrumentRental::create([
            'student_id' => $student->id, 'instrument_id' => $instrument->id,
            'academic_year_id' => $this->currentYear->id, 'start_date' => '2025-10-01',
            'monthly_fee' => 25, 'deposit' => 150, 'status' => 'active',
        ]);
        BookDistribution::create([
            'student_id' => $student->id, 'book_id' => $book->id,
            'academic_year_id' => $this->currentYear->id, 'distribution_date' => '2025-10-01',
            'quantity' => 1, 'price_paid' => 7.00,
        ]);
        Exam::create([
            'student_id' => $student->id, 'exam_type' => 'other', 'level' => 3,
            'subject' => 'theory', 'exam_date' => '2026-06-15', 'result' => 'pending',
        ]);

        $student->load(['instrumentRentals', 'bookDistributions', 'exams']);
        $this->assertCount(1, $student->instrumentRentals);
        $this->assertCount(1, $student->bookDistributions);
        $this->assertCount(1, $student->exams);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GAP DOCUMENTATI (design R6 §1, §4, §8) — i test li bloccano/segnalano
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GAP A — Stato cauzione esplicito (cuore di R6, §4): manca la distinzione
     * "noleggio restituito" ≠ "cauzione resa/trattenuta". Nessun campo dedicato.
     */
    public function test_gap_stato_cauzione_esplicito_assente(): void
    {
        foreach (['deposit_status', 'deposit_returned_at', 'deposit_withheld_amount', 'deposit_withheld_reason'] as $col) {
            $this->assertFalse(
                Schema::hasColumn('instrument_rentals', $col),
                "GAP §4/§8: instrument_rentals non ha {$col} (stato cauzione esplicito assente)."
            );
        }
        $this->assertNotContains('deposit_status', (new InstrumentRental())->getFillable());

        // Oggi lo stato cauzione si approssima solo da status+return_date: lo strumento
        // può rientrare ("returned") senza che il sistema sappia se i soldi sono resi.
        $rental = new InstrumentRental(['status' => 'returned', 'deposit' => 150, 'return_date' => '2025-06-04']);
        $this->assertNull($rental->getAttribute('deposit_status'),
            'GAP: nessun stato cauzione — "restituito" non dice se la cauzione è resa o trattenuta.');
    }

    /**
     * GAP B — Materiali / accessori (design §5c, §8): entità StudentAccessory assente
     * (model, tabella, relazione su Student, CRUD/route).
     */
    public function test_gap_materiali_accessori_assenti(): void
    {
        $this->assertFalse(class_exists(\App\Models\StudentAccessory::class),
            'GAP §8: manca il model StudentAccessory (materiali/accessori).');
        $this->assertFalse(Schema::hasTable('student_accessories'),
            'GAP §8: manca la tabella student_accessories.');
        $this->assertFalse(method_exists(Student::class, 'accessories') || method_exists(Student::class, 'studentAccessories'),
            'GAP §8: Student non ha la relazione accessori/studentAccessories.');

        $routes = collect(app('router')->getRoutes())->map->getName()->filter()->values();
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains((string) $n, 'student-accessories') || str_contains((string) $n, 'accessories')),
            'GAP §8: nessuna route CRUD per i materiali/accessori.'
        );
    }

    /**
     * GAP C — Vista unificata per studente (design §2, §3): manca il tab "Materiali"
     * che aggrega noleggi/libri/materiali/esami per studente+anno. Oggi sono 4 liste
     * globali separate (controller distinti), nessun controller/route di aggregazione.
     */
    public function test_gap_vista_unificata_per_studente_assente(): void
    {
        $routes = collect(app('router')->getRoutes())->map->getName()->filter()->values();

        // Nessuna route che mostri il "tab materiali" sullo studente.
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains((string) $n, 'students.materials')
                || str_contains((string) $n, 'students.materiali')),
            'GAP §3: nessuna route "tab Materiali" sulla scheda studente.'
        );

        // I 4 mondi restano CRUD globali separati (no aggregatore unico).
        foreach (['admin.instrument-rentals.index', 'admin.book-distributions.index', 'admin.exams.index'] as $name) {
            $this->assertTrue($routes->contains($name), "Atteso CRUD separato: {$name}.");
        }
    }

    /**
     * BUG — Disallineamento controller ↔ enum DB su Exam (emerso nel controllo finale).
     * ExamController valida exam_type in {abrsm,lcm,internal,other} e subject come
     * stringa libera, ma la migrazione vincola exam_type a {ABRSM,LCM,other}
     * (case-sensitive su SQLite) e subject a {instrument,theory,both}.
     * Conseguenza: il caso realistico del design §5d (Ente ABRSM · Materia "Pianoforte")
     * supera la validazione del controller ma viola il CHECK del DB → INSERT in errore.
     */
    public function test_bug_exam_type_e_subject_disallineati_controller_vs_db(): void
    {
        // Lato controller, 'abrsm' (lowercase) e 'internal' sono accettati (nessun errore di validazione)...
        $this->assertMatchesRegularExpression(
            '/abrsm|internal/',
            'abrsm,lcm,internal,other',
            'Il controller dichiara exam_type in {abrsm,lcm,internal,other}.'
        );

        // ...ma il vincolo DB li rifiuta: 'abrsm' != 'ABRSM' e 'internal' non è in enum.
        $student = $this->makeStudent('Mario', 'Rossi');
        $rejected = false;
        try {
            Exam::create([
                'student_id' => $student->id, 'exam_type' => 'abrsm', 'level' => 3,
                'subject' => 'Pianoforte', 'exam_date' => '2026-06-15', 'result' => 'pending',
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            $rejected = true;
        }
        $this->assertTrue($rejected,
            'BUG: exam_type=abrsm / subject libero passano il controller ma il DB li rifiuta (enum disallineato).');
        $this->assertSame(0, Exam::count());
    }

    /**
     * GAP D — Esiti esame "merito" / "distinzione" (design §5d, §8 microcopy):
     * il design li elenca, ma la validazione del controller accetta solo
     * passed/failed/pending. Esiti graduati non rappresentabili.
     */
    public function test_gap_esiti_esame_merito_distinzione_assenti(): void
    {
        $student = $this->makeStudent('Mario', 'Rossi');

        $this->actingAs($this->user)
            ->post(route('admin.exams.store'), [
                'student_id' => $student->id,
                'exam_type' => 'abrsm',
                'level' => 3,
                'subject' => 'Pianoforte',
                'exam_date' => '2026-06-15',
                'result' => 'merit', // non in enum {passed,failed,pending}
            ])
            ->assertSessionHasErrors('result');

        $this->assertSame(0, Exam::count(), 'GAP §8: gli esiti merito/distinzione non sono accettati.');
    }
}
