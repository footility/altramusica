@extends('family.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header">Attiva il tuo accesso all'area famiglie</div>
            <div class="card-body">
                <p class="text-muted">
                    Ciao {{ $invitation->guardian->full_name }}, imposta una password e accetta l'informativa
                    privacy per attivare l'accesso. Il link scade tra pochi giorni.
                </p>
                <form method="POST" action="{{ route('family.invitation.activate', $invitation->token) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ $invitation->email }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Almeno 8 caratteri, con lettere e numeri.</div>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Conferma password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input @error('privacy_accept') is-invalid @enderror"
                               type="checkbox" name="privacy_accept" id="privacy_accept" value="1">
                        <label class="form-check-label" for="privacy_accept">
                            Ho letto e accetto l'<a href="{{ route('privacy.policy') }}" target="_blank">informativa privacy</a>
                            @if($privacyVersion) (v. {{ $privacyVersion }}) @endif
                        </label>
                        @error('privacy_accept')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Attiva l'accesso</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
