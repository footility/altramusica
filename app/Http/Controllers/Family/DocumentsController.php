<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentsController extends Controller
{
    /**
     * Archivio documenti condivisi con la famiglia.
     * Mostra solo i documenti dello studente di sessione, flag shared_with_family.
     */
    public function index(Request $request)
    {
        $student = $request->attributes->get('family_student');

        $documents = $student->documents()
            ->sharedWithFamily()
            ->orderByDesc('created_at')
            ->get();

        return view('family.documents.index', compact('student', 'documents'));
    }

    /**
     * Download di un documento condiviso.
     * Valida che il documento appartenga allo studente di sessione e sia condiviso.
     */
    public function download(Request $request, Document $document): StreamedResponse
    {
        $student = $request->attributes->get('family_student');

        abort_unless(
            $document->student_id === $student->id && $document->shared_with_family,
            404
        );

        $disk = Storage::disk($document->disk ?: 'local');
        abort_unless($disk->exists($document->path), 404);

        $downloadName = $document->name;
        $ext = pathinfo($document->path, PATHINFO_EXTENSION);
        if ($ext && ! str_ends_with(strtolower($downloadName), '.'.strtolower($ext))) {
            $downloadName .= '.'.$ext;
        }

        return $disk->download($document->path, $downloadName);
    }
}
