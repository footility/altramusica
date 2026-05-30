<?php /* family/communications/index.blade.php */ ?>
@extends('layouts.family')

@section('family-content')
<div class="container py-4">
    <h1 class="h4 mb-1">Comunicazioni</h1>
    <p class="text-muted">Tutte le comunicazioni rivolte alla famiglia di {{ $student->first_name }}.</p>

    <div class="card">
        <div class="card-body">
            @forelse($communications as $com)
                <div class="border-bottom py-3">
                    <div class="d-flex justify-content-between">
                        <a class="fw-semibold text-decoration-none" href="{{ route('family.communications.show', $com) }}">{{ $com->title }}</a>
                        <small class="text-muted">{{ optional($com->published_at)->format('d/m/Y') }}</small>
                    </div>
                    <p class="mb-0 text-muted">{{ Str::limit($com->body, 160) }}</p>
                </div>
            @empty
                <p class="text-muted mb-0">Nessuna comunicazione.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-3">
        {{ $communications->links() }}
    </div>
</div>
@endsection
