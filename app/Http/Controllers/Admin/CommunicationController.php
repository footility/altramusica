<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Communication;
use App\Models\Student;
use App\Models\Guardian;
use App\Services\CommunicationService;
use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    protected $communicationService;

    public function __construct(CommunicationService $communicationService)
    {
        $this->communicationService = $communicationService;
    }

    public function index(Request $request)
    {
        $query = Communication::with(['student', 'guardian', 'sentBy']);
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        $communications = $query->orderBy('created_at', 'desc')
            ->paginate(50);
        
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.communications.index', compact('communications', 'students'));
    }
    
    public function create(Request $request)
    {
        $student = null;
        $guardian = null;
        
        if ($request->has('student_id')) {
            $student = Student::findOrFail($request->student_id);
        }
        
        if ($request->has('guardian_id')) {
            $guardian = Guardian::findOrFail($request->guardian_id);
        }
        
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $guardians = Guardian::orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.communications.create', compact('student', 'guardian', 'students', 'guardians'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:email,sms,whatsapp',
            'student_id' => 'nullable|exists:students,id',
            'guardian_id' => 'nullable|exists:guardians,id',
            'subject' => 'required_if:type,email|nullable|string|max:255',
            'message' => 'required|string',
            'template_name' => 'nullable|string|max:255',
        ]);
        
        if (empty($validated['student_id']) && empty($validated['guardian_id'])) {
            return back()->withErrors(['error' => 'Seleziona almeno uno studente o un genitore'])->withInput();
        }
        
        $communication = $this->communicationService->sendCommunication($validated);
        
        if ($communication->status === 'delivered') {
            return redirect()->route('admin.communications.index')
                ->with('success', 'Comunicazione inviata con successo.');
        } else {
            return redirect()->route('admin.communications.show', $communication)
                ->with('error', 'Errore durante l\'invio: ' . ($communication->error_message ?? 'Errore sconosciuto'));
        }
    }
    
    public function show(Communication $communication)
    {
        $communication->load(['student', 'guardian', 'sentBy']);
        
        return view('admin.communications.show', compact('communication'));
    }
    
    public function bulk(Request $request)
    {
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        
        return view('admin.communications.bulk', compact('students'));
    }
    
    public function sendBulk(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:email,sms,whatsapp',
            'subject' => 'required_if:type,email|nullable|string|max:255',
            'message' => 'required|string',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);
        
        $results = $this->communicationService->sendBulk(
            $validated['type'],
            $validated['subject'] ?? '',
            $validated['message'],
            ['student_ids' => $validated['student_ids']]
        );
        
        $success = count(array_filter($results, fn($r) => $r->status === 'delivered'));
        $failed = count($results) - $success;
        
        return redirect()->route('admin.communications.index')
            ->with('success', "Comunicazioni inviate: {$success} successo, {$failed} errori.");
    }
}
