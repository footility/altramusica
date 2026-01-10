<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Services\ContractService;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    protected $contractService;
    protected $academicYearService;

    public function __construct(ContractService $contractService, AcademicYearService $academicYearService)
    {
        $this->contractService = $contractService;
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        $query = Contract::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
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

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $contracts = $query->with(['student', 'academicYear'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.contracts.index', compact('contracts', 'students', 'years', 'currentYear'));
    }

    public function create(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        $preselectedStudent = $request->get('student_id') ? Student::find($request->get('student_id')) : null;

        return view('admin.contracts.create', compact('students', 'years', 'currentYear', 'preselectedStudent'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'student_id' => 'required|exists:students,id',
            'contract_number' => 'nullable|string|unique:contracts,contract_number',
            'type' => 'required|in:regular,short,summer,instrument_rental',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:draft,sent,signed,expired,cancelled',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (!$validated['academic_year_id']) {
            $currentYear = $this->academicYearService->getCurrent();
            $validated['academic_year_id'] = $currentYear?->id;
        }

        if (!$validated['contract_number']) {
            $validated['contract_number'] = $this->contractService->generateContractNumber();
        }

        // Genera token per link precompilato
        $validated['token'] = Str::random(64);

        $contract = Contract::create($validated);

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contratto creato con successo.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['student', 'academicYear', 'documents']);
        return view('admin.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $contract->load(['student', 'academicYear']);
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.contracts.edit', compact('contract', 'students', 'years'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'student_id' => 'required|exists:students,id',
            'contract_number' => 'nullable|string|unique:contracts,contract_number,' . $contract->id,
            'type' => 'required|in:regular,short,summer,instrument_rental',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:draft,sent,signed,expired,cancelled',
            'terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $contract->update($validated);

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contratto aggiornato con successo.');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('admin.contracts.index')
            ->with('success', 'Contratto eliminato con successo.');
    }

    public function send(Contract $contract)
    {
        $this->contractService->sendContract($contract);

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Contratto inviato.');
    }

    public function sign(Contract $contract)
    {
        $this->contractService->signContract($contract);

        return redirect()->route('admin.contracts.show', $contract)
            ->with('success', 'Contratto firmato.');
    }
}
