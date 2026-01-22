<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\PaymentPlan;
use App\Models\Student;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;

class PaymentPlanController extends Controller
{
    protected AcademicYearService $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        $query = PaymentPlan::query();

        // join via invoice for academic_year + student search
        $query->with(['invoice.student', 'invoice.academicYear', 'payment']);

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->whereHas('invoice.student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('tax_code', 'like', "%{$search}%");
            })->orWhereHas('invoice', function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('academic_year_id')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        } elseif ($currentYear) {
            $query->whereHas('invoice', function ($q) use ($currentYear) {
                $q->where('academic_year_id', $currentYear->id);
            });
        }

        if ($request->filled('student_id')) {
            $query->whereHas('invoice', function ($q) use ($request) {
                $q->where('student_id', $request->student_id);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'overdue') {
                $query->overdue();
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('from_due_date')) {
            $query->where('due_date', '>=', $request->from_due_date);
        }

        if ($request->filled('to_due_date')) {
            $query->where('due_date', '<=', $request->to_due_date);
        }

        $installments = $query
            ->orderBy('due_date')
            ->paginate(30);

        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();

        $statuses = [
            'pending' => 'Da pagare',
            'paid' => 'Pagata',
            'overdue' => 'Scaduta',
        ];

        return view('admin.payment-plans.index', compact(
            'installments',
            'years',
            'students',
            'statuses',
            'currentYear'
        ));
    }
}

