<?php

namespace Tests\Feature\Family;

use App\Models\FamilySession;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FamilyReceiptsTest extends TestCase
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

    private function makeInvoice(Student $student, array $attrs = []): Invoice
    {
        return Invoice::create(array_merge([
            'student_id' => $student->id,
            'number' => 'INV-'.Str::random(4),
            'issued_at' => now(),
            'total' => 100,
            'status' => 'paid',
        ], $attrs));
    }

    public function test_index_lists_only_paid_invoices(): void
    {
        $student = $this->makeStudent();
        $this->makeInvoice($student, ['number' => 'PAID-1', 'status' => 'paid']);
        $this->makeInvoice($student, ['number' => 'DRAFT-1', 'status' => 'draft']);
        $this->makeInvoice($student, ['number' => 'OVERDUE-1', 'status' => 'overdue']);

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.receipts'))
            ->assertOk()
            ->assertSee('PAID-1')
            ->assertDontSee('DRAFT-1')
            ->assertDontSee('OVERDUE-1');
    }

    public function test_download_paid_receipt(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, ['number' => 'PAID-9']);

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.receipts.download', $invoice))
            ->assertOk()
            ->assertSee('RICEVUTA')
            ->assertSee('PAID-9');
    }

    public function test_download_denies_unpaid_invoice(): void
    {
        $student = $this->makeStudent();
        $invoice = $this->makeInvoice($student, ['status' => 'draft']);

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.receipts.download', $invoice))
            ->assertNotFound();
    }

    public function test_download_denies_other_students_receipt(): void
    {
        $student = $this->makeStudent();
        $other = $this->makeStudent('Luigi');
        $invoice = $this->makeInvoice($other);

        $token = $this->actingAsFamily($student);

        $this->withCookie('family_session', $token)
            ->get(route('family.receipts.download', $invoice))
            ->assertNotFound();
    }
}
