<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Privacy' }} — {{ config('privacy.controller.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 820px;">
        <div class="bg-white p-4 p-md-5 rounded shadow-sm">
            <a href="{{ url('/') }}" class="text-decoration-none small">&larr; Torna al gestionale</a>
            <h1 class="h3 mt-3 mb-4">@yield('heading')</h1>

            <div class="alert alert-warning small">
                <strong>Bozza.</strong> Testo da validare con il consulente privacy del cliente
                prima della pubblicazione definitiva.
            </div>

            @yield('content')

            <hr class="my-4">
            <p class="small text-muted mb-0">
                Versione {{ config('privacy.policy_version') }} ·
                <a href="{{ route('privacy.policy') }}">Privacy policy</a> ·
                <a href="{{ route('privacy.cookies') }}">Cookie policy</a>
            </p>
        </div>
    </div>
</body>
</html>
