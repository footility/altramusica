<?php

namespace App\Http\Controllers\Family;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Family\Concerns\ScopesToGuardian;
use App\Models\Document;
use App\Models\LoginLog;
use Illuminate\Support\Facades\Storage;

/**
 * R13 — Download documenti dall'area famiglie. Solo documenti di un figlio del
 * tutore E marcati visibili alla famiglia; ogni download è loggato (GDPR).
 */
class DocumentController extends Controller
{
    use ScopesToGuardian;

    public function download(string $document)
    {
        // Scoping: deve appartenere a un figlio accessibile ed essere condiviso.
        $doc = Document::visibleToFamily()
            ->whereIn('student_id', $this->childIds())
            ->find($document);

        abort_if($doc === null, 404, 'Documento non disponibile.');

        abort_unless(Storage::exists($doc->file_path), 404, 'Documento non disponibile.');

        // Tracciabilità del download (riuso di LoginLog come registro accessi).
        LoginLog::create([
            'user_id' => auth()->id(),
            'email' => auth()->user()->email,
            'event' => 'family_document_download',
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 1000),
            'created_at' => now(),
        ]);

        return Storage::download($doc->file_path, $doc->file_name);
    }
}
