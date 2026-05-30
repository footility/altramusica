<?php /* family/documents/index.blade.php */ ?>
@extends('layouts.family')

@section('family-content')
<div class="container py-4">
    <h1 class="h4 mb-1">Documenti</h1>
    <p class="text-muted">Documenti condivisi dalla scuola per {{ $student->first_name }}.</p>

    <div class="card">
        <div class="card-body p-0">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th class="d-none d-md-table-cell">Tipo</th>
                        <th class="d-none d-md-table-cell">Data</th>
                        <th class="d-none d-md-table-cell">Dimensione</th>
                        <th class="text-end">Azione</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr>
                            <td>{{ $doc->name }}</td>
                            <td class="d-none d-md-table-cell">{{ $doc->type ?? '—' }}</td>
                            <td class="d-none d-md-table-cell">{{ optional($doc->created_at)->format('d/m/Y') }}</td>
                            <td class="d-none d-md-table-cell">
                                {{ $doc->size ? number_format($doc->size / 1024, 0, ',', '.').' KB' : '—' }}
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-primary" href="{{ route('family.documents.download', $doc) }}">Scarica</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted text-center py-4">Nessun documento condiviso al momento.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
