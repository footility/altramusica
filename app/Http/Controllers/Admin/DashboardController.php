<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentYear;
use App\Models\Enrollment;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\PaymentPlan;
use App\Models\Lesson;
use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    protected $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function index()
    {
        $currentYear = $this->academicYearService->getCurrent();

        if (!$currentYear) {
            return view('admin.dashboard', [
                'currentYear' => null,
                'stats' => [],
                'weekLessons' => collect(),
                'weekLessonsCount' => 0,
                'incompleteStudents' => collect(),
                'incompleteCount' => 0,
                'dueSoon' => collect(),
                'overdueCount' => 0,
            ])->with('warning', 'Nessun anno scolastico attivo. Configura un anno scolastico per iniziare.');
        }

        // Statistiche per anno corrente
        $stats = [
            'students' => [
                'total' => StudentYear::where('academic_year_id', $currentYear->id)->count(),
                'enrolled' => StudentYear::where('academic_year_id', $currentYear->id)
                    ->whereIn('status', ['enrolled'])
                    ->count(),
                'interested' => StudentYear::where('academic_year_id', $currentYear->id)
                    ->whereIn('status', ['interested', 'prospect'])
                    ->count(),
                'prospect' => StudentYear::where('academic_year_id', $currentYear->id)->where('status', 'prospect')->count(),
            ],
            'enrollments' => [
                'total' => Enrollment::where('academic_year_id', $currentYear->id)
                    ->count(),
                'active' => Enrollment::where(function($q) use ($currentYear) {
                        $q->where('academic_year_id', $currentYear->id);
                    })
                    ->where('status', 'active')
                    ->count(),
            ],
            'contracts' => [
                'total' => Contract::where('academic_year_id', $currentYear->id)->count(),
                'signed' => Contract::where('academic_year_id', $currentYear->id)
                    ->where('status', 'signed')
                    ->count(),
                'pending' => Contract::where('academic_year_id', $currentYear->id)
                    ->whereIn('status', ['draft', 'sent'])
                    ->count(),
            ],
            'invoices' => [
                'total' => Invoice::where('academic_year_id', $currentYear->id)->count(),
                'paid' => Invoice::where('academic_year_id', $currentYear->id)
                    ->whereIn('status', ['paid', 'completed'])
                    ->count(),
                'pending' => Invoice::where('academic_year_id', $currentYear->id)
                    ->whereIn('status', ['draft', 'pending', 'sent', 'overdue'])
                    ->count(),
                'total_amount' => Invoice::where('academic_year_id', $currentYear->id)->sum('total_amount') ?? 0,
                'paid_amount' => Invoice::where('academic_year_id', $currentYear->id)
                    ->whereIn('status', ['paid', 'completed'])
                    ->sum('total_amount') ?? 0,
            ],
        ];

        // Lezioni della settimana corrente (lun-dom)
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weekLessons = Lesson::with(['courseOffering.course', 'teacher'])
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('date')
            ->orderBy('time_start')
            ->get();
        $weekLessonsCount = $weekLessons->count();

        // Alert: anagrafiche incomplete (studenti dell'anno corrente senza codice fiscale,
        // senza data di nascita o senza alcun genitore/tutore collegato)
        $incompleteStudents = Student::whereNull('anonymized_at')
            ->whereHas('years', function ($q) use ($currentYear) {
                $q->where('academic_year_id', $currentYear->id);
            })
            ->where(function ($q) {
                $q->whereNull('tax_code')
                    ->orWhere('tax_code', '')
                    ->orWhereNull('birth_date')
                    ->orWhereDoesntHave('guardians');
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
        $incompleteCount = $incompleteStudents->count();

        // Prossime scadenze (rate dei piani di pagamento entro 7 giorni) + scadute
        $today = Carbon::now()->startOfDay();
        $dueSoon = PaymentPlan::with(['invoice.student'])
            ->where('status', 'pending')
            ->whereBetween('due_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()])
            ->orderBy('due_date')
            ->get();
        $overdueCount = PaymentPlan::where('status', 'pending')
            ->whereDate('due_date', '<', $today->toDateString())
            ->count();

        return view('admin.dashboard', compact(
            'currentYear',
            'stats',
            'weekLessons',
            'weekLessonsCount',
            'incompleteStudents',
            'incompleteCount',
            'dueSoon',
            'overdueCount'
        ));
    }
}
