<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Contract;
use App\Models\AcademicYear;
use App\Services\InvoiceService;
use App\Services\ContractService;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    protected $invoiceService;
    protected $contractService;
    protected $academicYearService;

    public function __construct(
        InvoiceService $invoiceService,
        ContractService $contractService,
        AcademicYearService $academicYearService
    ) {
        $this->invoiceService = $invoiceService;
        $this->contractService = $contractService;
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        $query = Invoice::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        } elseif ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->with(['student', 'academicYear'])
            ->orderBy('invoice_date', 'desc')
            ->paginate(20);

        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.invoices.index', compact('invoices', 'students', 'years', 'currentYear'));
    }

    public function create(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        $preselectedStudent = $request->get('student_id') ? Student::find($request->get('student_id')) : null;

        return view('admin.invoices.create', compact('students', 'years', 'currentYear', 'preselectedStudent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'student_id' => 'required|exists:students,id',
            'invoice_number' => 'nullable|string|unique:invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (!$validated['academic_year_id']) {
            $currentYear = $this->academicYearService->getCurrent();
            $validated['academic_year_id'] = $currentYear?->id;
        }

        if (!$validated['invoice_number']) {
            $validated['invoice_number'] = $this->invoiceService->generateInvoiceNumber();
        }

        $invoice = Invoice::create($validated);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Fattura creata con successo.');
    }

    public function createFromContract(Contract $contract)
    {
        $invoice = $this->invoiceService->createFromContract($contract);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Fattura creata dal contratto.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['student', 'academicYear', 'items', 'payments', 'paymentPlans', 'creditNotes']);
        return view('admin.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['student', 'academicYear']);
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.invoices.edit', compact('invoice', 'students', 'years'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'student_id' => 'required|exists:students,id',
            'invoice_number' => 'nullable|string|unique:invoices,invoice_number,' . $invoice->id,
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Fattura aggiornata con successo.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Fattura eliminata con successo.');
    }

    public function createPaymentPlan(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'number_of_installments' => 'required|integer|min:1|max:12',
            'first_due_date' => 'required|date',
        ]);

        $this->invoiceService->createPaymentPlan(
            $invoice,
            $validated['number_of_installments'],
            Carbon::parse($validated['first_due_date'])
        );

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Piano di pagamento creato.');
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:bank_transfer,cash,check,card,other',
            'payment_date' => 'required|date',
        ]);

        $this->invoiceService->recordPayment(
            $invoice,
            $validated['amount'],
            $validated['payment_method'],
            Carbon::parse($validated['payment_date'])
        );

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Pagamento registrato.');
    }
}
