<?php

namespace Tests\Feature\Family;

use App\Models\Communication;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * R13 (#8538) — Comunicazioni ricevute consultabili dall'area famiglie.
 * Sola lettura, scopata sul tutore e sui suoi figli; solo comunicazioni
 * effettivamente inviate (status sent/delivered).
 */
class FamilyCommunicationsTest extends TestCase
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

    private function makeCommunication(array $attrs = []): Communication
    {
        return Communication::create(array_merge([
            'type' => 'email',
            'subject' => 'Comunicazione',
            'message' => 'Corpo della comunicazione.',
            'sent_at' => now(),
            'status' => 'sent',
        ], $attrs));
    }

    public function test_index_lists_own_and_childrens_communications(): void
    {
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id);
        $user = $this->makeFamilyUser($guardian);

        $this->makeCommunication(['student_id' => $figlio->id, 'subject' => 'PER-FIGLIO']);
        $this->makeCommunication(['guardian_id' => $guardian->id, 'subject' => 'PER-TUTORE']);

        // Non inviata: non deve comparire.
        $this->makeCommunication(['student_id' => $figlio->id, 'subject' => 'FALLITA', 'status' => 'failed']);

        // Di un'altra famiglia: fuori perimetro.
        $altro = $this->makeGuardian(['first_name' => 'Bruno']);
        $altrui = $this->makeStudent('Luca', 'Bianchi');
        $altro->students()->attach($altrui->id);
        $this->makeCommunication(['student_id' => $altrui->id, 'subject' => 'ALTRUI']);

        $this->actingAs($user)->get(route('family.communications.index'))
            ->assertOk()
            ->assertSee('PER-FIGLIO')
            ->assertSee('PER-TUTORE')
            ->assertDontSee('FALLITA')
            ->assertDontSee('ALTRUI');
    }

    public function test_show_own_communication(): void
    {
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id);
        $user = $this->makeFamilyUser($guardian);

        $com = $this->makeCommunication(['student_id' => $figlio->id, 'subject' => 'Avviso saggio']);

        $this->actingAs($user)->get(route('family.communications.show', $com))
            ->assertOk()
            ->assertSee('Avviso saggio');
    }

    public function test_show_denies_other_familys_communication(): void
    {
        $guardian = $this->makeGuardian();
        $user = $this->makeFamilyUser($guardian);

        $altro = $this->makeGuardian(['first_name' => 'Bruno']);
        $altrui = $this->makeStudent('Luca', 'Bianchi');
        $altro->students()->attach($altrui->id);
        $com = $this->makeCommunication(['student_id' => $altrui->id, 'subject' => 'Altrui']);

        $this->actingAs($user)->get(route('family.communications.show', $com))
            ->assertNotFound();
    }

    public function test_show_denies_unsent_communication(): void
    {
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id);
        $user = $this->makeFamilyUser($guardian);

        $com = $this->makeCommunication(['student_id' => $figlio->id, 'status' => 'failed']);

        $this->actingAs($user)->get(route('family.communications.show', $com))
            ->assertNotFound();
    }

    public function test_index_requires_family_session(): void
    {
        $this->get(route('family.communications.index'))->assertRedirect(route('login'));
    }
}
