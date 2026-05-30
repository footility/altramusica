<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Family\Concerns\ScopesToGuardian;
use App\Models\Document;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\PaymentPlan;

/**
 * R13 — Dashboard area famiglie: dati dello studente, prossime lezioni,
 * scadenze, documenti. Tutto sola lettura e scopato sui figli del tutore.
 */
class DashboardController extends Controller
{
    use ScopesToGuardian;

    public function index()
    {
        $children = $this->children();
        $childIds = $children->pluck('id')->all();

        return view('family.dashboard', [
            'guardian' => $this->guardian(),
            'children' => $children,
            'lessons' => $this->upcomingLessons($childIds),
            'deadlines' => $this->deadlines($childIds),
            'documents' => $this->documents($childIds),
        ]);
    }

    public function student(string $student)
    {
        $child = $this->childOrFail($student);
        $child->load(['currentYear.academicYear', 'enrollments.course', 'enrollments.academicYear']);

        return view('family.student', [
            'child' => $child,
            'lessons' => $this->upcomingLessons([$child->id]),
            'deadlines' => $this->deadlines([$child->id]),
            'documents' => $this->documents([$child->id]),
        ]);
    }

    /** Prossime lezioni dei corsi a cui i figli sono iscritti. */
    protected function upcomingLessons(array $childIds)
    {
        if (empty($childIds)) {
            return collect();
        }

        $offeringIds = Enrollment::whereIn('student_id', $childIds)
            ->whereNotNull('course_offering_id')
            ->pluck('course_offering_id')
            ->unique()
            ->all();

        if (empty($offeringIds)) {
            return collect();
        }

        return Lesson::whereIn('course_offering_id', $offeringIds)
            ->whereDate('date', '>=', today())
            ->with(['teacher', 'classroom', 'course'])
            ->orderBy('date')
            ->orderBy('time_start')
            ->limit(10)
            ->get();
    }

    /** Rate dovute (da pagare / in ritardo) dei figli, dalla più vicina. */
    protected function deadlines(array $childIds)
    {
        if (empty($childIds)) {
            return collect();
        }

        return PaymentPlan::whereHas('invoice', fn ($q) => $q->whereIn('student_id', $childIds))
            ->whereIn('status', ['pending', 'overdue'])
            ->with('invoice.student')
            ->orderBy('due_date')
            ->get();
    }

    /** Documenti esplicitamente condivisi con la famiglia. */
    protected function documents(array $childIds)
    {
        if (empty($childIds)) {
            return collect();
        }

        return Document::whereIn('student_id', $childIds)
            ->visibleToFamily()
            ->with('student')
            ->latest()
            ->get();
    }
}
