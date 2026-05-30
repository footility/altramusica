<?php

namespace Tests\Feature\Family;

use App\Models\FamilyRequest;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * R13 (#8539) — Canale richieste famiglia → gestionale.
 * Apertura, scoping sul tutore/figli, thread bidirezionale e stati gestiti
 * dalla segreteria.
 */
class FamilyRequestsChannelTest extends TestCase
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
            'email_1' => 'anna' . uniqid() . '@example.test',
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

    private function makeStaff(): User
    {
        $staff = User::factory()->create();
        $staff->assignRole('admin');

        return $staff;
    }

    public function test_famiglia_apre_una_richiesta_per_un_proprio_figlio(): void
    {
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id, ['is_primary' => true]);
        $user = $this->makeFamilyUser($guardian);

        $this->actingAs($user)->post(route('family.requests.store'), [
            'category' => 'didattica',
            'student_id' => $figlio->id,
            'subject' => 'Cambio orario lezione',
            'body' => 'Sarebbe possibile spostare la lezione del lunedì?',
        ])->assertRedirect();

        $req = FamilyRequest::firstOrFail();
        $this->assertSame($guardian->id, $req->guardian_id);
        $this->assertSame($figlio->id, $req->student_id);
        $this->assertSame(FamilyRequest::STATUS_NEW, $req->status);
        $this->assertSame(1, $req->messages()->count());
        $this->assertSame('family', $req->messages()->first()->author_role);
    }

    public function test_non_puo_riferire_la_richiesta_al_figlio_di_un_altro(): void
    {
        $guardian = $this->makeGuardian();
        $user = $this->makeFamilyUser($guardian);

        $altro = $this->makeGuardian(['first_name' => 'Bruno']);
        $altrui = $this->makeStudent('Luca', 'Bianchi');
        $altro->students()->attach($altrui->id);

        $this->actingAs($user)->post(route('family.requests.store'), [
            'category' => 'altro',
            'student_id' => $altrui->id,
            'subject' => 'Test',
            'body' => 'Corpo del messaggio',
        ])->assertNotFound();

        $this->assertDatabaseCount('family_requests', 0);
    }

    public function test_famiglia_vede_solo_le_proprie_richieste(): void
    {
        $guardian = $this->makeGuardian();
        $user = $this->makeFamilyUser($guardian);
        $mia = $guardian->familyRequests()->create([
            'category' => 'altro', 'subject' => 'Mia', 'status' => FamilyRequest::STATUS_NEW,
        ]);

        $altro = $this->makeGuardian(['first_name' => 'Bruno']);
        $altrui = $altro->familyRequests()->create([
            'category' => 'altro', 'subject' => 'Altrui', 'status' => FamilyRequest::STATUS_NEW,
        ]);

        $this->actingAs($user)->get(route('family.requests.index'))
            ->assertOk()
            ->assertSee('Mia')
            ->assertDontSee('Altrui');

        $this->actingAs($user)->get(route('family.requests.show', $altrui))->assertNotFound();
    }

    public function test_thread_bidirezionale_e_riapertura_su_risposta_famiglia(): void
    {
        $guardian = $this->makeGuardian();
        $user = $this->makeFamilyUser($guardian);
        $staff = $this->makeStaff();

        $req = $guardian->familyRequests()->create([
            'category' => 'altro', 'subject' => 'Domanda', 'status' => FamilyRequest::STATUS_NEW,
        ]);
        $req->messages()->create(['user_id' => $user->id, 'author_role' => 'family', 'body' => 'Ciao']);

        // Segreteria risponde e mette in attesa della famiglia.
        $this->actingAs($staff)->post(route('admin.family-requests.reply', $req), [
            'body' => 'Le facciamo sapere',
            'status' => FamilyRequest::STATUS_WAITING_FAMILY,
        ])->assertRedirect();

        $req->refresh();
        $this->assertSame(FamilyRequest::STATUS_WAITING_FAMILY, $req->status);
        $this->assertSame($staff->id, $req->assigned_to_user_id);
        $this->assertSame('staff', $req->last_message_role);

        // La famiglia risponde: torna in lavorazione.
        $this->actingAs($user)->post(route('family.requests.reply', $req), [
            'body' => 'Grazie, attendo',
        ])->assertRedirect();

        $req->refresh();
        $this->assertSame(FamilyRequest::STATUS_IN_PROGRESS, $req->status);
        $this->assertSame('family', $req->last_message_role);
        $this->assertSame(3, $req->messages()->count());
    }

    public function test_richiesta_chiusa_blocca_la_risposta_famiglia(): void
    {
        $guardian = $this->makeGuardian();
        $user = $this->makeFamilyUser($guardian);
        $req = $guardian->familyRequests()->create([
            'category' => 'altro', 'subject' => 'Chiusa', 'status' => FamilyRequest::STATUS_CLOSED,
        ]);

        $this->actingAs($user)->post(route('family.requests.reply', $req), [
            'body' => 'Posso ancora scrivere?',
        ])->assertForbidden();

        $this->assertSame(0, $req->messages()->count());
    }

    public function test_segreteria_filtra_inbox_per_stato_e_cambia_stato(): void
    {
        $staff = $this->makeStaff();
        $guardian = $this->makeGuardian();

        $nuova = $guardian->familyRequests()->create([
            'category' => 'altro', 'subject' => 'NuovaRich', 'status' => FamilyRequest::STATUS_NEW,
        ]);
        $guardian->familyRequests()->create([
            'category' => 'altro', 'subject' => 'ChiusaRich', 'status' => FamilyRequest::STATUS_CLOSED,
        ]);

        $this->actingAs($staff)->get(route('admin.family-requests.index', ['status' => FamilyRequest::STATUS_NEW]))
            ->assertOk()
            ->assertSee('NuovaRich')
            ->assertDontSee('ChiusaRich');

        $this->actingAs($staff)->patch(route('admin.family-requests.status', $nuova), [
            'status' => FamilyRequest::STATUS_RESOLVED,
        ])->assertRedirect();

        $nuova->refresh();
        $this->assertSame(FamilyRequest::STATUS_RESOLVED, $nuova->status);
        $this->assertNotNull($nuova->resolved_at);
        $this->assertSame($staff->id, $nuova->assigned_to_user_id);
    }

    public function test_area_famiglie_richiede_login(): void
    {
        // Guest → redirect al login (middleware auth standard).
        $this->get(route('family.requests.index'))
            ->assertRedirect(route('login'));
    }
}
