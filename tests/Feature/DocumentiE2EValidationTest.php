<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\DocumentController;
use App\Models\AcademicYear;
use App\Models\Contract;
use App\Models\Document;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * R10 · Controllo finale — Validazione documenti/modelli (attività #8542).
 *
 * Test E2E dell'archivio documenti che esercita SOLO ciò che esiste in codice
 * (modello Document, DocumentController CRUD + ricerca + filtri tipo/studente/
 * contratto, upload singolo) e documenta esplicitamente i gap rispetto al
 * design R10. Riferimenti: docs/23_UX_ARCHIVIO_DOCUMENTI_RICERCA_RAPIDA.md §1, §3-§5.
 */
class DocumentiE2EValidationTest extends TestCase
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

    /** Filtri index (controller diretto) per i 3 assi che HANNO supporto: tipo/studente/contratto + ricerca. */
    private function indexDocuments(array $params): \Illuminate\Support\Collection
    {
        $view = app(DocumentController::class)->index(new Request($params));

        return collect($view->getData()['documents']->items());
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CIÒ CHE FUNZIONA (deve passare esercitando il codice reale)
    // ─────────────────────────────────────────────────────────────────────────

    /** FLUSSO 2 — Upload: il form classico (file singolo) crea il Document e salva il file. */
    public function test_upload_documento_singolo_crea_record_e_salva_file(): void
    {
        Storage::fake('public');
        $student = $this->makeStudent('Mario', 'Rossi');

        $response = $this->actingAs($this->user)->post(route('admin.documents.store'), [
            'student_id' => $student->id,
            'type' => 'privacy',
            'file' => UploadedFile::fake()->create('Privacy_Rossi_Mario.pdf', 120, 'application/pdf'),
        ]);

        $document = Document::first();
        $this->assertNotNull($document, 'Lo store deve creare il Document.');
        $response->assertRedirect(route('admin.documents.show', $document));

        $this->assertDatabaseHas('documents', [
            'student_id' => $student->id,
            'type' => 'privacy',
            'file_name' => 'Privacy_Rossi_Mario.pdf',
            'uploaded_by_user_id' => $this->user->id,
        ]);
        Storage::disk('public')->assertExists($document->file_path);
    }

    /** Validazione store: tipo fuori enum e file mancante vengono rifiutati. */
    public function test_upload_rifiuta_tipo_non_valido_e_file_mancante(): void
    {
        Storage::fake('public');

        $this->actingAs($this->user)
            ->post(route('admin.documents.store'), ['type' => 'privacy'])
            ->assertSessionHasErrors('file');

        $this->actingAs($this->user)
            ->post(route('admin.documents.store'), [
                'type' => 'rental', // non in enum {contract,privacy,photo_consent,other}
                'file' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasErrors('type');

        $this->assertSame(0, Document::count());
    }

    /** FLUSSO 1 — Filtri archivio: tipo, studente, contratto, ricerca testo (combinabili). */
    public function test_filtri_archivio_tipo_studente_contratto_ricerca(): void
    {
        $mario = $this->makeStudent('Mario', 'Rossi');
        $luca = $this->makeStudent('Luca', 'Verdi');

        $contract = Contract::create([
            'academic_year_id' => $this->currentYear->id,
            'student_id' => $mario->id,
            'contract_number' => 'A-0142',
            'type' => 'course',
            'status' => 'draft',
            'start_date' => $this->currentYear->start_date,
            'end_date' => $this->currentYear->end_date,
        ]);

        $mk = fn (array $attr) => Document::create(array_merge([
            'file_path' => 'documents/x.pdf',
            'file_name' => 'x.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
            'uploaded_by_user_id' => $this->user->id,
        ], $attr));

        $mk(['student_id' => $mario->id, 'contract_id' => $contract->id, 'type' => 'contract', 'file_name' => 'Contratto_A-0142.pdf']);
        $mk(['student_id' => $mario->id, 'type' => 'privacy', 'file_name' => 'Privacy_Rossi.pdf']);
        $mk(['student_id' => $luca->id, 'type' => 'privacy', 'file_name' => 'Privacy_Verdi.pdf']);

        $this->assertCount(3, $this->indexDocuments([]), 'Senza filtri: tutti i documenti.');
        $this->assertCount(2, $this->indexDocuments(['type' => 'privacy']), 'Filtro tipo.');
        $this->assertCount(2, $this->indexDocuments(['student_id' => $mario->id]), 'Filtro studente.');
        $this->assertCount(1, $this->indexDocuments(['contract_id' => $contract->id]), 'Filtro contratto.');
        // Ricerca testo: nome file
        $this->assertCount(1, $this->indexDocuments(['search' => 'A-0142']), 'Ricerca per nome file.');
        // Ricerca testo: cognome studente (orWhereHas student)
        $this->assertCount(1, $this->indexDocuments(['search' => 'Verdi']), 'Ricerca per cognome studente.');
    }

    /** CRUD — update (cambio tipo) e destroy (rimuove record + file). */
    public function test_update_e_destroy_documento(): void
    {
        Storage::fake('public');
        $student = $this->makeStudent('Mario', 'Rossi');

        $this->actingAs($this->user)->post(route('admin.documents.store'), [
            'student_id' => $student->id,
            'type' => 'other',
            'file' => UploadedFile::fake()->create('doc.pdf', 50, 'application/pdf'),
        ]);
        $document = Document::firstOrFail();
        $path = $document->file_path;

        // update tipo other -> photo_consent
        $this->actingAs($this->user)
            ->put(route('admin.documents.update', $document), [
                'student_id' => $student->id,
                'type' => 'photo_consent',
            ])
            ->assertRedirect(route('admin.documents.index'));
        $this->assertSame('photo_consent', $document->fresh()->type);

        // destroy rimuove record e file su disco
        $this->actingAs($this->user)
            ->delete(route('admin.documents.destroy', $document))
            ->assertRedirect(route('admin.documents.index'));
        $this->assertSame(0, Document::count());
        Storage::disk('public')->assertMissing($path);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GAP DOCUMENTATI (design R10 §1 — non implementati): i test li bloccano/segnalano
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GAP A — Filtro per ANNO scolastico in 1 click (design §1, §3).
     * La tabella documents non ha academic_year_id: l'anno è desumibile solo via contract.
     */
    public function test_gap_filtro_anno_assente(): void
    {
        $this->assertFalse(
            Schema::hasColumn('documents', 'academic_year_id'),
            'GAP: documents non ha academic_year_id (design §1: filtro anno assente).'
        );
        $this->assertNotContains('academic_year_id', (new Document())->getFillable(),
            'GAP: Document non modella l\'anno scolastico.');

        // Il controller non riconosce il parametro "year": passandolo non filtra nulla.
        $mario = $this->makeStudent('Mario', 'Rossi');
        Document::create([
            'student_id' => $mario->id, 'type' => 'privacy',
            'file_path' => 'documents/a.pdf', 'file_name' => 'a.pdf',
            'mime_type' => 'application/pdf', 'size' => 1, 'uploaded_by_user_id' => $this->user->id,
        ]);
        $this->assertCount(1, $this->indexDocuments(['year' => $this->currentYear->id]),
            'GAP: il parametro year è ignorato dal controller (nessun filtro anno).');
    }

    /**
     * GAP B — Upload drag-drop multi-file (design §1, §4).
     * Lo store accetta UN solo file (regola "required|file"), nessun storeMany.
     */
    public function test_gap_upload_multifile_assente(): void
    {
        Storage::fake('public');

        $this->assertFalse(method_exists(DocumentController::class, 'storeMany'),
            'GAP: nessun endpoint multi-file (design §4).');

        // Inviando un array di file lo store fallisce la validazione "file" (atteso single).
        $this->actingAs($this->user)
            ->post(route('admin.documents.store'), [
                'type' => 'privacy',
                'file' => [
                    UploadedFile::fake()->create('a.pdf', 10, 'application/pdf'),
                    UploadedFile::fake()->create('b.pdf', 10, 'application/pdf'),
                ],
            ])
            ->assertSessionHasErrors('file');
        $this->assertSame(0, Document::count(), 'GAP: l\'upload multiplo non è supportato.');
    }

    /**
     * GAP C — Generazione documento dai dati / template (design §1, §5).
     * Nessun service di templating, nessuna route/azione "generate", nessun
     * campo che distingua i documenti "generated" dai "uploaded", nessun tipo "rental".
     */
    public function test_gap_generazione_da_template_assente(): void
    {
        $this->assertFalse(class_exists(\App\Services\DocumentTemplateService::class),
            'GAP: manca DocumentTemplateService (design §5/§8).');
        $this->assertFalse(method_exists(DocumentController::class, 'generate'),
            'GAP: DocumentController non espone azione di generazione.');

        $routes = collect(app('router')->getRoutes())->map->getName()->filter()->values();
        $this->assertFalse(
            $routes->contains(fn ($n) => str_contains((string) $n, 'documents.generate')),
            'GAP: nessuna route di generazione documento (design §5).'
        );

        // Nessun flag origine (uploaded/generated) né tipo "rental" (noleggio) nello schema.
        $this->assertFalse(Schema::hasColumn('documents', 'source'),
            'GAP: nessun flag origine uploaded/generated (design §5, §8).');
        $this->assertNotContains('source', (new Document())->getFillable());
    }
}
