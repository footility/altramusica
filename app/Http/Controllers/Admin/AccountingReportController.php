<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Invoice;
use App\Models\Student;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountingReportController extends Controller
{
    protected AcademicYearService $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function balances(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();

        $yearId = $request->filled('academic_year_id')
            ? (int) $request->academic_year_id
            : ($currentYear?->id ?? null);

        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();

        $query = Student::query()
            ->select([
                'students.id',
                'students.first_name',
                'students.last_name',
                'students.tax_code',
            ])
            ->addSelect([
                'invoiced_total' => Invoice::query()
                    ->selectRaw('COALESCE(SUM(total_amount), 0)')
                    ->whereColumn('student_id', 'students.id')
                    ->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId)),
                'paid_total' => DB::table('payments')
                    ->selectRaw('COALESCE(SUM(payments.amount), 0)')
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->whereColumn('invoices.student_id', 'students.id')
                    ->when($yearId, fn ($q) => $q->where('invoices.academic_year_id', $yearId)),
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search')->toString();
                $q->where(function ($sq) use ($search) {
                    $sq->where('students.first_name', 'like', "%{$search}%")
                        ->orWhere('students.last_name', 'like', "%{$search}%")
                        ->orWhere('students.tax_code', 'like', "%{$search}%");
                });
            });

        // Optional filter: only students with open balance
        if ($request->boolean('only_open', true)) {
            $query->havingRaw('(invoiced_total - paid_total) > 0.001');
        }

        $rows = $query
            ->orderBy('students.last_name')
            ->orderBy('students.first_name')
            ->paginate(30)
            ->withQueryString();

        return view('admin.accounting/balances', compact('rows', 'students', 'years', 'currentYear', 'yearId'));
    }
}

