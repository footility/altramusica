<?php

namespace Tests\Feature\Family;

use App\Models\Communication;
use App\Models\FamilySession;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FamilyCommunicationsTest extends TestCase
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

    private function makeComm(array $attrs = []): Communication
    {
        return Communication::create(array_merge([
            'student_id' => null,
            'title' => 'Avviso',
            'body' => 'Corpo comunicazione',
            'audience' => 'families',
            'published_at' => now(),
        ], $attrs));
    }

    public function test_index_lists_general_and_own_communications(): void
    {
        $student = $this->makeStudent();
        $other = $this->makeStudent('Luigi');

        $this->makeComm(['title' => 'Generale a tutti', 'student_id' => null]);
        $this->makeComm(['title' => 'Solo Mario', 'student_id' => $student->id]);
        $this->makeComm(['title' => 'Solo Luigi', 'student_id' => $other->id]);
        $this->makeComm(['title' => 'Per docenti', 'audience' => 'teachers', 'student_id' => null]);

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.communications'))
            ->assertOk()
            ->assertSee('Generale a tutti')
            ->assertSee('Solo Mario')
            ->assertDontSee('Solo Luigi')
            ->assertDontSee('Per docenti');
    }

    public function test_index_is_not_limited_to_ten(): void
    {
        $student = $this->makeStudent();
        for ($i = 1; $i <= 15; $i++) {
            $this->makeComm(['title' => 'Comunicazione '.$i, 'published_at' => now()->subDays($i)]);
        }

        $token = $this->actingAsFamily($student);

        // paginazione a 20 → la quindicesima è presente nella prima pagina
        $this->withCookie('family_session', $token)
            ->get(route('family.communications'))
            ->assertOk()
            ->assertSee('Comunicazione 15');
    }

    public function test_show_own_communication(): void
    {
        $student = $this->makeStudent();
        $comm = $this->makeComm(['title' => 'Dettaglio', 'student_id' => $student->id]);

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.communications.show', $comm))
            ->assertOk()
            ->assertSee('Dettaglio');
    }

    public function test_show_denies_other_students_communication(): void
    {
        $student = $this->makeStudent();
        $other = $this->makeStudent('Luigi');
        $comm = $this->makeComm(['student_id' => $other->id]);

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.communications.show', $comm))
            ->assertNotFound();
    }

    public function test_show_denies_non_family_audience(): void
    {
        $student = $this->makeStudent();
        $comm = $this->makeComm(['audience' => 'teachers', 'student_id' => null]);

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.communications.show', $comm))
            ->assertNotFound();
    }
}
