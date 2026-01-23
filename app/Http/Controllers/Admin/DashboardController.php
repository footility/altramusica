<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentYear;
use App\Models\Enrollment;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;

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

        return view('admin.dashboard', compact('currentYear', 'stats'));
    }
}
