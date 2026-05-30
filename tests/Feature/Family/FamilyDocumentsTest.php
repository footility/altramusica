<?php

namespace Tests\Feature\Family;

use App\Models\Document;
use App\Models\FamilySession;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class FamilyDocumentsTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsFamily(Student $student): string
    {
        $plain = Str::random(64);
        FamilySession::create([
            'student_id' => $student->id,
            'token' => hash('sha256', $plain),
            'expires_at' => now()->addDays(30),
        ]);

        return $plain;
    }

    private function makeStudent(string $first = 'Mario'): Student
    {
        return Student::create(['first_name' => $first, 'last_name' => 'Rossi']);
    }

    private function makeDoc(Student $student, array $attrs = []): Document
    {
        return Document::create(array_merge([
            'student_id' => $student->id,
            'name' => 'Modulo iscrizione',
            'type' => 'Modulo',
            'disk' => 'local',
            'path' => 'documents/'.Str::random(8).'.pdf',
            'size' => 1024,
            'mime' => 'application/pdf',
            'shared_with_family' => true,
        ], $attrs));
    }

    public function test_index_requires_family_session(): void
    {
        $this->get(route('family.documents'))->assertRedirect(route('family.login'));
    }

    public function test_lists_only_own_shared_documents(): void
    {
        $student = $this->makeStudent();
        $other = $this->makeStudent('Luigi');

        $this->makeDoc($student, ['name' => 'Certificato Mario']);
        $this->makeDoc($student, ['name' => 'Bozza interna', 'shared_with_family' => false]);
        $this->makeDoc($other, ['name' => 'Documento Luigi']);

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.documents'))
            ->assertOk()
            ->assertSee('Certificato Mario')
            ->assertDontSee('Bozza interna')
            ->assertDontSee('Documento Luigi');
    }

    public function test_download_own_shared_document(): void
    {
        Storage::fake('local');
        $student = $this->makeStudent();
        $doc = $this->makeDoc($student, ['path' => 'documents/mario.pdf']);
        Storage::disk('local')->put('documents/mario.pdf', 'contenuto');

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.documents.download', $doc))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    public function test_download_denies_other_students_document(): void
    {
        Storage::fake('local');
        $student = $this->makeStudent();
        $other = $this->makeStudent('Luigi');
        $doc = $this->makeDoc($other);

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.documents.download', $doc))
            ->assertNotFound();
    }

    public function test_download_denies_non_shared_document(): void
    {
        Storage::fake('local');
        $student = $this->makeStudent();
        $doc = $this->makeDoc($student, ['shared_with_family' => false]);

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.documents.download', $doc))
            ->assertNotFound();
    }
}
