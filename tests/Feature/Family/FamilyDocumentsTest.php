<?php

namespace Tests\Feature\Family;

use App\Models\Document;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * R13 (#8538) — Archivio documenti area famiglie.
 * Sola lettura, scopata sui figli del tutore: solo documenti marcati
 * visibili alla famiglia, mai documenti di altri.
 */
class FamilyDocumentsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('acl:sync', ['--reset-defaults' => true]);
    }

    private function makeGuardian(array $overrides = []): Guardian
    {
        return Guardian::create(array_merge([
            'first_name' => 'Anna',
            'last_name' => 'Rossi',
            'relationship' => 'mother',
            'email_1' => 'anna'.uniqid().'@example.test',
            'privacy_consent' => true,
        ], $overrides));
    }

    private function makeStudent(string $first = 'Marco', string $last = 'Rossi'): Student
    {
        return Student::create(['first_name' => $first, 'last_name' => $last]);
    }

    private function makeFamilyUser(Guardian $guardian): User
    {
        $user = User::create([
            'name' => $guardian->full_name,
            'email' => $guardian->email_1,
            'password' => bcrypt('password123'),
            'guardian_id' => $guardian->id,
        ]);
        $user->assignRole('family');

        return $user;
    }

    private function makeDocument(Student $student, bool $visible, string $name): Document
    {
        return Document::create([
            'student_id' => $student->id,
            'type' => 'other',
            'visible_to_family' => $visible,
            'file_path' => 'documents/'.$name,
            'file_name' => $name,
        ]);
    }

    public function test_index_lists_only_own_shared_documents(): void
    {
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id, ['is_primary' => true]);
        $user = $this->makeFamilyUser($guardian);

        $this->makeDocument($figlio, true, 'condiviso.pdf');
        $this->makeDocument($figlio, false, 'interno.pdf');

        $altro = $this->makeGuardian(['first_name' => 'Bruno']);
        $altrui = $this->makeStudent('Luca', 'Bianchi');
        $altro->students()->attach($altrui->id);
        $this->makeDocument($altrui, true, 'altrui.pdf');

        $this->actingAs($user)->get(route('family.documents.index'))
            ->assertOk()
            ->assertSee('condiviso.pdf')
            ->assertDontSee('interno.pdf')
            ->assertDontSee('altrui.pdf');
    }

    public function test_download_own_shared_document(): void
    {
        Storage::fake();
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id);
        $user = $this->makeFamilyUser($guardian);

        $doc = $this->makeDocument($figlio, true, 'ricevuta.pdf');
        Storage::put($doc->file_path, 'contenuto');

        $this->actingAs($user)->get(route('family.document.download', $doc))
            ->assertOk();
    }

    public function test_download_denies_other_students_document(): void
    {
        Storage::fake();
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id);
        $user = $this->makeFamilyUser($guardian);

        $altro = $this->makeGuardian(['first_name' => 'Bruno']);
        $altrui = $this->makeStudent('Luca', 'Bianchi');
        $altro->students()->attach($altrui->id);
        $doc = $this->makeDocument($altrui, true, 'altrui.pdf');
        Storage::put($doc->file_path, 'contenuto');

        $this->actingAs($user)->get(route('family.document.download', $doc))
            ->assertNotFound();
    }

    public function test_download_denies_non_shared_document(): void
    {
        Storage::fake();
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id);
        $user = $this->makeFamilyUser($guardian);

        $doc = $this->makeDocument($figlio, false, 'interno.pdf');
        Storage::put($doc->file_path, 'contenuto');

        $this->actingAs($user)->get(route('family.document.download', $doc))
            ->assertNotFound();
    }

    public function test_index_requires_family_session(): void
    {
        $this->get(route('family.documents.index'))->assertRedirect(route('login'));
    }
}
