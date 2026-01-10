<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AcademicYearController extends Controller
{
    protected $service;

    public function __construct(AcademicYearService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $years = AcademicYear::orderBy('start_date', 'desc')->get();
        return view('admin.academic-years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:academic_years,slug',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $year = AcademicYear::create($validated);

        if ($validated['is_active'] ?? false) {
            $this->service->setCurrent($year);
        }

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Anno scolastico creato con successo.');
    }

    public function show(AcademicYear $academicYear)
    {
        $academicYear->loadCount(['students', 'enrollments', 'contracts', 'invoices']);
        return view('admin.academic-years.show', compact('academicYear'));
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:academic_years,slug,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $academicYear->update($validated);

        if ($validated['is_active'] ?? false) {
            $this->service->setCurrent($academicYear);
        }

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Anno scolastico aggiornato con successo.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_active) {
            return redirect()->route('admin.academic-years.index')
                ->with('error', 'Non puoi eliminare l\'anno scolastico attivo.');
        }

        $academicYear->delete();

        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Anno scolastico eliminato con successo.');
    }

    public function setActive(AcademicYear $academicYear)
    {
        $this->service->setCurrent($academicYear);
        
        return redirect()->route('admin.academic-years.index')
            ->with('success', 'Anno scolastico attivato.');
    }
}
