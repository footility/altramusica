<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuthAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Genera permessi + ruoli (admin pieno, segreteria senza contabilità, teacher base).
        Artisan::call('acl:sync', ['--reset-defaults' => true]);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_admin_can_access_contabilita(): void
    {
        $this->actingAs($this->makeUser('admin'))
            ->get('/admin/invoices')
            ->assertOk();
    }

    public function test_segreteria_cannot_access_contabilita(): void
    {
        $segreteria = $this->makeUser('segreteria');

        $this->actingAs($segreteria)->get('/admin/invoices')->assertForbidden();
        $this->actingAs($segreteria)->get('/admin/accounting/balances')->assertForbidden();
        $this->actingAs($segreteria)->get('/admin/payment-plans')->assertForbidden();
    }

    public function test_segreteria_can_access_anagrafiche(): void
    {
        $this->actingAs($this->makeUser('segreteria'))
            ->get('/admin/students')
            ->assertOk();
    }

    public function test_segreteria_cannot_access_acl_and_logs(): void
    {
        $segreteria = $this->makeUser('segreteria');

        $this->actingAs($segreteria)->get('/admin/acl')->assertForbidden();
        $this->actingAs($segreteria)->get('/admin/login-logs')->assertForbidden();
    }

    public function test_successful_login_is_logged(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret-pass-1')]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret-pass-1',
        ]);

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('login_logs', [
            'user_id' => $user->id,
            'event' => 'login',
        ]);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_failed_login_is_logged(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret-pass-1')]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $this->assertDatabaseHas('login_logs', [
            'email' => $user->email,
            'event' => 'failed',
        ]);
    }
}
