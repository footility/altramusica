@extends('layouts.admin')

@section('title', 'Ruoli e permessi')
@section('page-title', 'Ruoli e permessi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Ruoli e permessi</h2>
    <form action="{{ route('admin.acl.roles.store') }}" method="POST" class="d-flex gap-2">
        @csrf
        <input type="text" name="name" class="form-control form-control-sm" placeholder="Nuovo ruolo (es. coordinatore)" required style="min-width:220px">
        <button class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-circle"></i> Crea ruolo</button>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

{{-- Selettore ruolo --}}
<ul class="nav nav-pills mb-3">
    @foreach($roles as $r)
        <li class="nav-item">
            <a class="nav-link {{ $role && $role->id === $r->id ? 'active' : '' }}"
               href="{{ route('admin.acl.index', ['role' => $r->name]) }}">
                {{ ucfirst($r->name) }}
                @if($r->name === 'admin') <i class="bi bi-shield-lock-fill ms-1"></i> @endif
            </a>
        </li>
    @endforeach
</ul>

@if($role)
<form action="{{ route('admin.acl.update', $role) }}" method="POST">
    @csrf
    @method('PUT')

    @if($locked)
        <div class="alert alert-info">
            <i class="bi bi-shield-lock-fill"></i> Il ruolo <strong>admin</strong> ha sempre tutti i permessi e non è modificabile.
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="min-width:240px">Sezione / Entità</th>
                        @foreach($actions as $key => $label)
                            <th class="text-center" style="width:90px">
                                {{ $label }}
                                <br>
                                <input type="checkbox" class="form-check-input acl-col" data-action="{{ $key }}"
                                       title="Seleziona tutta la colonna" {{ $locked ? 'disabled' : '' }}>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($sections as $section => $entities)
                        <tr class="table-secondary">
                            <td colspan="{{ count($actions) + 1 }}" class="fw-bold">{{ $section }}</td>
                        </tr>
                        @foreach($entities as $entity => $entityLabel)
                            <tr>
                                <td>
                                    {{ $entityLabel }}
                                    <small class="text-muted">({{ $entity }})</small>
                                </td>
                                @foreach($actions as $key => $label)
                                    @php $perm = "{$entity}.{$key}"; @endphp
                                    <td class="text-center">
                                        <input type="checkbox"
                                               class="form-check-input acl-cell"
                                               data-action="{{ $key }}"
                                               name="permissions[{{ $perm }}]"
                                               value="1"
                                               {{ $locked || $granted->has($perm) ? 'checked' : '' }}
                                               {{ $locked ? 'disabled' : '' }}>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @unless($locked)
        <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-check-circle"></i> Salva permessi</button>
            <a href="{{ route('admin.acl.index', ['role' => $role->name]) }}" class="btn btn-outline-secondary">Annulla</a>
        </div>
    @endunless
</form>

<script>
    // "Seleziona tutta la colonna" per azione
    document.querySelectorAll('.acl-col').forEach(function (col) {
        col.addEventListener('change', function () {
            var action = this.dataset.action;
            document.querySelectorAll('.acl-cell[data-action="' + action + '"]').forEach(function (cb) {
                if (!cb.disabled) cb.checked = col.checked;
            });
        });
    });
</script>
@else
    <div class="alert alert-warning">Nessun ruolo presente. Crea il primo ruolo qui sopra.</div>
@endif
@endsection
