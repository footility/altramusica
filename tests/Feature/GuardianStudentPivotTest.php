<?php

namespace Tests\Feature;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianStudentPivotTest extends TestCase
{
    use RefreshDatabase;

    private function actingAdmin(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    private function makeStudent(string $first, string $last): Student
    {
        return Student::create([
            'first_name' => $first,
            'last_name' => $last,
        ]);
    }

    private function basePayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Anna',
            'last_name' => 'Rossi',
            'relationship' => 'mother',
            'privacy_consent' => 1,
        ], $overrides);
    }

    public function test_store_sets_pivot_fields_for_first_student(): void
    {
        $this->actingAdmin();
        $figlio1 = $this->makeStudent('Marco', 'Rossi');
        $figlio2 = $this->makeStudent('Luca', 'Rossi');

        $this->post(route('admin.guardians.store'), $this->basePayload([
            'student_ids' => [$figlio1->id, $figlio2->id],
        ]))->assertRedirect(route('admin.guardians.index'));

        $guardian = Guardian::firstOrFail();

        $p1 = $guardian->students()->where('students.id', $figlio1->id)->first()->pivot;
        $p2 = $guardian->students()->where('students.id', $figlio2->id)->first()->pivot;

        $this->assertSame('mother', $p1->relationship_type);
        $this->assertTrue((bool) $p1->is_primary);
        $this->assertTrue((bool) $p1->is_billing_contact);

        // Secondo figlio: stesso rapporto ma non primary/billing.
        $this->assertSame('mother', $p2->relationship_type);
        $this->assertFalse((bool) $p2->is_primary);
        $this->assertFalse((bool) $p2->is_billing_contact);
    }

    public function test_update_preserves_pivot_fields_for_two_children_one_guardian(): void
    {
        $this->actingAdmin();
        $figlio1 = $this->makeStudent('Marco', 'Rossi');
        $figlio2 = $this->makeStudent('Luca', 'Rossi');

        $guardian = Guardian::create([
            'first_name' => 'Anna',
            'last_name' => 'Rossi',
            'relationship' => 'mother',
        ]);

        // Stato iniziale del pivot (come da store).
        $guardian->students()->attach($figlio1->id, [
            'relationship_type' => 'mother',
            'is_primary' => true,
            'is_billing_contact' => true,
        ]);
        $guardian->students()->attach($figlio2->id, [
            'relationship_type' => 'mother',
            'is_primary' => false,
            'is_billing_contact' => false,
        ]);

        // Update del genitore mantenendo entrambi i figli.
        $this->put(route('admin.guardians.update', $guardian), $this->basePayload([
            'first_name' => 'Anna Maria',
            'student_ids' => [$figlio1->id, $figlio2->id],
        ]))->assertRedirect(route('admin.guardians.index'));

        $guardian->refresh();
        $p1 = $guardian->students()->where('students.id', $figlio1->id)->first()->pivot;
        $p2 = $guardian->students()->where('students.id', $figlio2->id)->first()->pivot;

        // I pivot fields NON devono andare persi dopo l'update.
        $this->assertSame('mother', $p1->relationship_type);
        $this->assertTrue((bool) $p1->is_primary);
        $this->assertTrue((bool) $p1->is_billing_contact);

        $this->assertSame('mother', $p2->relationship_type);
        $this->assertFalse((bool) $p2->is_primary);
        $this->assertFalse((bool) $p2->is_billing_contact);

        $this->assertSame('Anna Maria', $guardian->first_name);
    }

    public function test_update_sets_pivot_for_newly_added_student(): void
    {
        $this->actingAdmin();
        $figlio1 = $this->makeStudent('Marco', 'Rossi');
        $figlio2 = $this->makeStudent('Luca', 'Rossi');

        $guardian = Guardian::create([
            'first_name' => 'Anna',
            'last_name' => 'Rossi',
            'relationship' => 'mother',
        ]);
        $guardian->students()->attach($figlio1->id, [
            'relationship_type' => 'mother',
            'is_primary' => true,
            'is_billing_contact' => true,
        ]);

        // Aggiunge il secondo figlio via update.
        $this->put(route('admin.guardians.update', $guardian), $this->basePayload([
            'student_ids' => [$figlio1->id, $figlio2->id],
        ]))->assertRedirect(route('admin.guardians.index'));

        $p2 = $guardian->students()->where('students.id', $figlio2->id)->first()->pivot;

        // Nuova associazione: pivot valorizzato, ma non secondo primary.
        $this->assertSame('mother', $p2->relationship_type);
        $this->assertFalse((bool) $p2->is_primary);
        $this->assertFalse((bool) $p2->is_billing_contact);
    }

    public function test_update_detaches_removed_student(): void
    {
        $this->actingAdmin();
        $figlio1 = $this->makeStudent('Marco', 'Rossi');
        $figlio2 = $this->makeStudent('Luca', 'Rossi');

        $guardian = Guardian::create([
            'first_name' => 'Anna',
            'last_name' => 'Rossi',
            'relationship' => 'mother',
        ]);
        $guardian->students()->attach([$figlio1->id, $figlio2->id]);

        $this->put(route('admin.guardians.update', $guardian), $this->basePayload([
            'student_ids' => [$figlio1->id],
        ]))->assertRedirect(route('admin.guardians.index'));

        $this->assertEqualsCanonicalizing(
            [$figlio1->id],
            $guardian->students()->pluck('students.id')->all()
        );
    }
}
