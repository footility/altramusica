<?php

namespace Tests\Feature\Family;

use App\Models\Guardian;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * R13 (#8538) — Ricevute pagamenti scaricabili dall'area famiglie.
 * Solo fatture saldate (status = paid) dei figli del tutore.
 */
class FamilyReceiptsTest extends TestCase
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

    private function makeInvoice(Student $student, array $attrs = []): Invoice
    {
        return Invoice::create(array_merge([
            'student_id' => $student->id,
            'invoice_number' => 'INV-'.Str::upper(Str::random(6)),
            'invoice_date' => now()->subDay(),
            'due_date' => now()->addDays(10),
            'total_amount' => 100,
            'status' => 'paid',
        ], $attrs));
    }

    public function test_index_lists_only_paid_invoices(): void
    {
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id);
        $user = $this->makeFamilyUser($guardian);

        $this->makeInvoice($figlio, ['invoice_number' => 'PAID-1', 'status' => 'paid']);
        $this->makeInvoice($figlio, ['invoice_number' => 'DRAFT-1', 'status' => 'draft']);
        $this->makeInvoice($figlio, ['invoice_number' => 'OVERDUE-1', 'status' => 'overdue']);

        $this->actingAs($user)->get(route('family.receipts.index'))
            ->assertOk()
            ->assertSee('PAID-1')
            ->assertDontSee('DRAFT-1')
            ->assertDontSee('OVERDUE-1');
    }

    public function test_download_paid_receipt(): void
    {
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id);
        $user = $this->makeFamilyUser($guardian);

        $invoice = $this->makeInvoice($figlio, ['invoice_number' => 'PAID-9']);

        $this->actingAs($user)->get(route('family.receipts.download', $invoice))
            ->assertOk()
            ->assertSee('RICEVUTA')
            ->assertSee('PAID-9');
    }

    public function test_download_denies_unpaid_invoice(): void
    {
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id);
        $user = $this->makeFamilyUser($guardian);

        $invoice = $this->makeInvoice($figlio, ['status' => 'draft']);

        $this->actingAs($user)->get(route('family.receipts.download', $invoice))
            ->assertNotFound();
    }

    public function test_download_denies_other_students_receipt(): void
    {
        $guardian = $this->makeGuardian();
        $figlio = $this->makeStudent();
        $guardian->students()->attach($figlio->id);
        $user = $this->makeFamilyUser($guardian);

        $altro = $this->makeGuardian(['first_name' => 'Bruno']);
        $altrui = $this->makeStudent('Luca', 'Bianchi');
        $altro->students()->attach($altrui->id);
        $invoice = $this->makeInvoice($altrui);

        $this->actingAs($user)->get(route('family.receipts.download', $invoice))
            ->assertNotFound();
    }

    public function test_index_requires_family_session(): void
    {
        $this->get(route('family.receipts.index'))->assertRedirect(route('login'));
    }
}
