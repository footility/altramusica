<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Instrument;
use App\Models\InstrumentRental;
use App\Models\Student;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;

class InstrumentRentalController extends Controller
{
    protected AcademicYearService $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();
        $query = InstrumentRental::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('tax_code', 'like', "%{$search}%");
            })->orWhereHas('instrument', function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        } elseif ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('instrument_id')) {
            $query->where('instrument_id', $request->instrument_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rentals = $query
            ->with(['student', 'instrument', 'academicYear'])
            ->orderBy('start_date', 'desc')
            ->paginate(20);

        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $instruments = Instrument::orderBy('type')->orderBy('brand')->get();

        $statuses = [
            'active' => 'Attivo',
            'returned' => 'Restituito',
            'cancelled' => 'Annullato',
        ];

        return view('admin.instrument-rentals.index', compact(
            'rentals',
            'years',
            'students',
            'instruments',
            'statuses',
            'currentYear'
        ));
    }

    public function create(Request $request)
    {
        $currentYear = $this->academicYearService->getCurrent();

        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $instruments = Instrument::orderBy('type')->orderBy('brand')->get();

        $preselectedStudent = $request->get('student_id') ? Student::find($request->get('student_id')) : null;

        return view('admin.instrument-rentals.create', compact(
            'years',
            'students',
            'instruments',
            'currentYear',
            'preselectedStudent'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'student_id' => 'required|exists:students,id',
            'instrument_id' => 'required|exists:instruments,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'monthly_fee' => 'nullable|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,returned,cancelled',
            'return_date' => 'nullable|date|after_or_equal:start_date',
            'return_condition' => 'nullable|in:excellent,good,fair,poor',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['academic_year_id'])) {
            $currentYear = $this->academicYearService->getCurrent();
            $validated['academic_year_id'] = $currentYear?->id;
        }

        $rental = InstrumentRental::create($validated);

        // Mark instrument as rented if active
        if ($validated['status'] === 'active') {
            Instrument::whereKey($validated['instrument_id'])->update(['status' => 'rented']);
        }

        return redirect()->route('admin.instrument-rentals.show', $rental)
            ->with('success', 'Noleggio creato con successo.');
    }

    public function show(InstrumentRental $instrumentRental)
    {
        $instrumentRental->load(['student', 'instrument', 'academicYear']);
        return view('admin.instrument-rentals.show', ['rental' => $instrumentRental]);
    }

    public function edit(InstrumentRental $instrumentRental)
    {
        $currentYear = $this->academicYearService->getCurrent();

        $instrumentRental->load(['student', 'instrument', 'academicYear']);

        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $instruments = Instrument::orderBy('type')->orderBy('brand')->get();

        $statuses = [
            'active' => 'Attivo',
            'returned' => 'Restituito',
            'cancelled' => 'Annullato',
        ];

        return view('admin.instrument-rentals.edit', compact(
            'instrumentRental',
            'years',
            'students',
            'instruments',
            'statuses',
            'currentYear'
        ));
    }

    public function update(Request $request, InstrumentRental $instrumentRental)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'student_id' => 'required|exists:students,id',
            'instrument_id' => 'required|exists:instruments,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'monthly_fee' => 'nullable|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,returned,cancelled',
            'return_date' => 'nullable|date|after_or_equal:start_date',
            'return_condition' => 'nullable|in:excellent,good,fair,poor',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['academic_year_id'])) {
            $currentYear = $this->academicYearService->getCurrent();
            $validated['academic_year_id'] = $currentYear?->id;
        }

        $oldInstrumentId = $instrumentRental->instrument_id;
        $oldStatus = $instrumentRental->status;

        $instrumentRental->update($validated);

        // Update instrument status in a conservative way
        if ($oldInstrumentId !== (int) $validated['instrument_id']) {
            // old instrument: if it was active rental, release it
            if ($oldStatus === 'active') {
                Instrument::whereKey($oldInstrumentId)->update(['status' => 'available']);
            }
        }

        if ($validated['status'] === 'active') {
            Instrument::whereKey($validated['instrument_id'])->update(['status' => 'rented']);
        } elseif ($validated['status'] === 'returned' || $validated['status'] === 'cancelled') {
            Instrument::whereKey($validated['instrument_id'])->update(['status' => 'available']);
        }

        return redirect()->route('admin.instrument-rentals.index')
            ->with('success', 'Noleggio aggiornato con successo.');
    }

    public function destroy(InstrumentRental $instrumentRental)
    {
        if ($instrumentRental->status === 'active') {
            Instrument::whereKey($instrumentRental->instrument_id)->update(['status' => 'available']);
        }

        $instrumentRental->delete();

        return redirect()->route('admin.instrument-rentals.index')
            ->with('success', 'Noleggio eliminato con successo.');
    }
}

