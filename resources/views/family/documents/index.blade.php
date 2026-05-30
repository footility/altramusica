@extends('family.layout')

@section('content')
<h1 class="h3 mb-1">Documenti</h1>
<p class="text-muted">Archivio dei documenti condivisi dalla scuola.</p>

<div class="card">
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th class="d-none d-md-table-cell">Allievo</th>
                    <th class="d-none d-md-table-cell">Tipo</th>
                    <th class="d-none d-md-table-cell">Data</th>
                    <th class="d-none d-md-table-cell">Dimensione</th>
                    <th class="text-end">Azione</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td>{{ $doc->file_name }}</td>
                        <td class="d-none d-md-table-cell">{{ $doc->student?->full_name ?? '—' }}</td>
                        <td class="d-none d-md-table-cell">{{ $doc->type ?? '—' }}</td>
                        <td class="d-none d-md-table-cell">{{ optional($doc->created_at)->format('d/m/Y') }}</td>
                        <td class="d-none d-md-table-cell">
                            {{ $doc->size ? number_format($doc->size / 1024, 0, ',', '.').' KB' : '—' }}
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-primary" href="{{ route('family.document.download', $doc) }}">Scarica</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-muted text-center py-4">Nessun documento condiviso al momento.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
