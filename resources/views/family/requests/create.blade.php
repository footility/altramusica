@extends('family.layout')

@section('content')
<a href="{{ route('family.requests.index') }}" class="btn btn-link px-0 mb-2">&larr; Le tue richieste</a>
<h1 class="h3 mb-3">Nuova richiesta</h1>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('family.requests.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="category">Argomento</label>
                <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}" @selected(old('category') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            @if($children->isNotEmpty())
                <div class="mb-3">
                    <label class="form-label" for="student_id">Riferito a (facoltativo)</label>
                    <select name="student_id" id="student_id" class="form-select">
                        <option value="">— Richiesta generale —</option>
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" @selected((string) old('student_id') === (string) $child->id)>{{ $child->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label" for="subject">Oggetto</label>
                <input type="text" name="subject" id="subject" maxlength="150" value="{{ old('subject') }}"
                       class="form-control @error('subject') is-invalid @enderror" required>
                @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="body">Messaggio</label>
                <textarea name="body" id="body" rows="6" maxlength="4000"
                          class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Invia alla segreteria</button>
        </form>
    </div>
</div>
@endsection
