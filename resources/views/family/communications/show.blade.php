<?php /* family/communications/show.blade.php */ ?>
@extends('layouts.family')

@section('family-content')
<div class="container py-4" style="max-width: 720px;">
    <a class="text-decoration-none" href="{{ route('family.communications') }}">&larr; Torna alle comunicazioni</a>

    <div class="card mt-3">
        <div class="card-body">
            <h1 class="h5 mb-1">{{ $communication->title }}</h1>
            <p class="text-muted">{{ optional($communication->published_at)->format('d/m/Y H:i') }}</p>
            <div class="mt-3">{!! nl2br(e($communication->body)) !!}</div>
        </div>
    </div>
</div>
@endsection
