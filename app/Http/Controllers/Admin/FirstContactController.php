<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FirstContact;
use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use Illuminate\Http\Request;

class FirstContactController extends Controller
{
    protected $academicYearService;

    public function __construct(AcademicYearService $academicYearService)
    {
        $this->academicYearService = $academicYearService;
    }

    public function index(Request $request)
    {
        $query = FirstContact::query();
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $firstContacts = $query->with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.first-contacts.index', compact('firstContacts'));
    }
    
    public function show(FirstContact $firstContact)
    {
        $firstContact->load('student');
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        
        return view('admin.first-contacts.show', compact('firstContact', 'academicYears'));
    }
    
    public function convert(Request $request, FirstContact $firstContact)
    {
        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);
        
        $academicYearId = $validated['academic_year_id'] ?? null;
        
        if (!$academicYearId) {
            $currentYear = $this->academicYearService->getCurrent();
            $academicYearId = $currentYear ? $currentYear->id : null;
        }
        
        $student = $firstContact->convertToStudent($academicYearId);
        
        return redirect()->route('admin.first-contacts.show', $firstContact)
            ->with('success', "Primo contatto convertito in studente: {$student->first_name} {$student->last_name}");
    }
    
    public function dismiss(FirstContact $firstContact)
    {
        $firstContact->update(['status' => 'dismissed']);
        
        return redirect()->route('admin.first-contacts.index')
            ->with('success', 'Primo contatto scartato.');
    }
    
    public function generateLink(FirstContact $firstContact)
    {
        if ($firstContact->status !== 'pending') {
            return redirect()->route('admin.first-contacts.show', $firstContact)
                ->with('error', 'Impossibile generare link per un contatto già processato.');
        }
        
        $url = route('public.first-contact.show', ['token' => $firstContact->token]);
        
        return redirect()->route('admin.first-contacts.show', $firstContact)
            ->with('link', $url);
    }
}
