<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Guardian;
use App\Models\GuardianInvitation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * R13 (#8537) — Accesso famiglia + dashboard studente.
 * Verifica invito/attivazione, login famiglia, scoping server-side e isolamento backoffice.
 */
class AreaFamiglieAccessoDashboardTest extends TestCase
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
            'email_1' => 'anna@example.test',
            'privacy_consent' => true,
        ], $overrides));
    }

    private function makeStudent(string $first, string $last): Student
    {
        return Student::create(['first_name' => $first, 'last_name' => $last]);
    }

    /** Crea un account famiglia attivo collegato al tutore. */
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

    public function test_invito_attivazione_crea_account_family_collegato_al_tutore(): void
    {
        $guardian = $this->makeGuardian();
        $invitation = GuardianInvitation::generateFor($guardian, $guardian->email_1);

        $this->get(route('family.invitation.show', $invitation->token))->assertOk();

        $this->post(route('family.invitation.activate', $invitation->token), [
            'password' => 'segreta123',
            'password_confirmation' => 'segreta123',
            'privacy_accept' => '1',
        ])->assertRedirect(route('family.dashboard'));

        $user = User::where('guardian_id', $guardian->id)->firstOrFail();
        $this->assertTrue($user->isFamily());
        $this->assertNotNull($invitation->fresh()->accepted_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_attivazione_richiede_consenso_privacy(): void
    {
        $guardian = $this->makeGuardian();
        $invitation = GuardianInvitation::generateFor($guardian, $guardian->email_1);

        $this->post(route('family.invitation.activate', $invitation->token), [
            'password' => 'segreta123',
            'password_confirmation' => 'segreta123',
            // privacy_accept mancante
        ])->assertSessionHasErrors('privacy_accept');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_token_scaduto_o_usato_ritorna_404(): void
    {
        $guardian = $this->makeGuardian();

        $expired = GuardianInvitation::generateFor($guardian, $guardian->email_1);
        $expired->forceFill(['expires_at' => now()->subDay()])->save();
        $this->get(route('family.invitation.show', $expired->token))->assertNotFound();

        $used = GuardianInvitation::generateFor($guardian, $guardian->email_1);
        $used->forceFill(['accepted_at' => now()])->save();
        $this->get(route('family.invitation.show', $used->token))->assertNotFound();
    }

    public function test_login_famiglia_riuscito_porta_alla_dashboard(): void
    {
        $guardian = $this->makeGuardian();
        $this->makeFamilyUser($guardian);

        $this->post(route('family.login.attempt'), [
            'email' => $guardian->email_1,
            'password' => 'password123',
        ])->assertRedirect(route('family.dashboard'));

        $this->assertAuthenticated();
    }

    public function test_dashboard_mostra_solo_i_propri_figli(): void
    {
        $guardian = $this->makeGuardian();
        $mio = $this->makeStudent('Mario', 'Rossi');
        $guardian->students()->attach($mio->id, ['is_primary' => true]);

        $altroGuardian = $this->makeGuardian(['first_name' => 'Beatrice', 'email_1' => 'bea@example.test']);
        $altrui = $this->makeStudent('Sofia', 'Verdi');
        $altroGuardian->students()->attach($altrui->id);

        $user = $this->makeFamilyUser($guardian);

        $res = $this->actingAs($user)->get(route('family.dashboard'))->assertOk();
        $res->assertSee('Mario Rossi');
        $res->assertDontSee('Sofia Verdi');
    }

    public function test_accesso_a_studente_fuori_perimetro_ritorna_404(): void
    {
        $guardian = $this->makeGuardian();
        $altroGuardian = $this->makeGuardian(['email_1' => 'bea@example.test']);
        $altrui = $this->makeStudent('Sofia', 'Verdi');
        $altroGuardian->students()->attach($altrui->id);

        $user = $this->makeFamilyUser($guardian);

        $this->actingAs($user)->get(route('family.student', $altrui->id))->assertNotFound();
    }

    public function test_download_documento_solo_se_condiviso_e_del_proprio_figlio(): void
    {
        Storage::fake('local');
        Storage::put('docs/contratto.pdf', 'contenuto');

        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent('Mario', 'Rossi');
        $guardian->students()->attach($figlio->id);
        $user = $this->makeFamilyUser($guardian);

        $condiviso = Document::create([
            'student_id' => $figlio->id, 'type' => 'contract', 'visible_to_family' => true,
            'file_path' => 'docs/contratto.pdf', 'file_name' => 'contratto.pdf',
        ]);
        $interno = Document::create([
            'student_id' => $figlio->id, 'type' => 'other', 'visible_to_family' => false,
            'file_path' => 'docs/contratto.pdf', 'file_name' => 'interno.pdf',
        ]);

        // Altro studente, documento condiviso ma fuori perimetro.
        $altroGuardian = $this->makeGuardian(['email_1' => 'bea@example.test']);
        $altrui = $this->makeStudent('Sofia', 'Verdi');
        $altroGuardian->students()->attach($altrui->id);
        $altruiDoc = Document::create([
            'student_id' => $altrui->id, 'type' => 'contract', 'visible_to_family' => true,
            'file_path' => 'docs/contratto.pdf', 'file_name' => 'altrui.pdf',
        ]);

        $this->actingAs($user)->get(route('family.document.download', $condiviso))->assertOk();
        $this->actingAs($user)->get(route('family.document.download', $interno))->assertNotFound();
        $this->actingAs($user)->get(route('family.document.download', $altruiDoc))->assertNotFound();

        // Il download condiviso è loggato (registro accessi GDPR).
        $this->assertDatabaseHas('login_logs', ['event' => 'family_document_download', 'user_id' => $user->id]);
    }

    public function test_utente_family_non_entra_nel_backoffice(): void
    {
        $guardian = $this->makeGuardian();
        $user = $this->makeFamilyUser($guardian);

        $this->actingAs($user)->get('/admin/dashboard')->assertRedirect(route('family.dashboard'));
    }

    public function test_admin_non_entra_nell_area_famiglie(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get(route('family.dashboard'))->assertForbidden();
    }

    public function test_invito_bloccato_senza_consenso_o_email(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $senzaConsenso = $this->makeGuardian(['privacy_consent' => false]);

        $this->actingAs($admin)
            ->post(route('admin.guardians.invite', $senzaConsenso))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('guardian_invitations', 0);
    }
}
