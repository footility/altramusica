<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Document;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::query();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            })->orWhereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('contract_id')) {
            $query->where('contract_id', $request->contract_id);
        }

        $documents = $query
            ->with(['student', 'contract', 'uploadedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $contracts = Contract::orderBy('created_at', 'desc')->get();
        $types = [
            'contract' => 'Contratto',
            'privacy' => 'Privacy',
            'photo_consent' => 'Consenso Foto',
            'other' => 'Altro',
        ];

        return view('admin.documents.index', compact('documents', 'students', 'contracts', 'types'));
    }

    public function create(Request $request)
    {
        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $contracts = Contract::orderBy('created_at', 'desc')->get();
        $types = [
            'contract' => 'Contratto',
            'privacy' => 'Privacy',
            'photo_consent' => 'Consenso Foto',
            'other' => 'Altro',
        ];

        $preselectedStudent = $request->get('student_id') ? Student::find($request->get('student_id')) : null;
        $preselectedContract = $request->get('contract_id') ? Contract::find($request->get('contract_id')) : null;

        return view('admin.documents.create', compact(
            'students',
            'contracts',
            'types',
            'preselectedStudent',
            'preselectedContract'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'type' => 'required|in:contract,privacy,photo_consent,other',
            'file' => 'required|file|max:20480',
        ]);

        $file = $request->file('file');
        $path = $file->store('documents', 'public');

        $document = Document::create([
            'student_id' => $validated['student_id'] ?? null,
            'contract_id' => $validated['contract_id'] ?? null,
            'type' => $validated['type'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'uploaded_by_user_id' => auth()->id(),
        ]);

        return redirect()->route('admin.documents.show', $document)
            ->with('success', 'Documento caricato con successo.');
    }

    public function show(Document $document)
    {
        $document->load(['student', 'contract', 'uploadedBy']);
        $publicUrl = $document->file_path ? Storage::disk('public')->url($document->file_path) : null;

        return view('admin.documents.show', compact('document', 'publicUrl'));
    }

    public function edit(Document $document)
    {
        $document->load(['student', 'contract']);

        $students = Student::orderBy('last_name')->orderBy('first_name')->get();
        $contracts = Contract::orderBy('created_at', 'desc')->get();
        $types = [
            'contract' => 'Contratto',
            'privacy' => 'Privacy',
            'photo_consent' => 'Consenso Foto',
            'other' => 'Altro',
        ];

        return view('admin.documents.edit', compact('document', 'students', 'contracts', 'types'));
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'type' => 'required|in:contract,privacy,photo_consent,other',
            'file' => 'nullable|file|max:20480',
        ]);

        $payload = [
            'student_id' => $validated['student_id'] ?? null,
            'contract_id' => $validated['contract_id'] ?? null,
            'type' => $validated['type'],
        ];

        if ($request->hasFile('file')) {
            // delete old
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('documents', 'public');
            $payload['file_path'] = $path;
            $payload['file_name'] = $file->getClientOriginalName();
            $payload['mime_type'] = $file->getClientMimeType();
            $payload['size'] = $file->getSize();
            $payload['uploaded_by_user_id'] = auth()->id();
        }

        $document->update($payload);

        return redirect()->route('admin.documents.index')
            ->with('success', 'Documento aggiornato.');
    }

    public function destroy(Document $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', 'Documento eliminato.');
    }
}

