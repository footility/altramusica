<?php /* layouts/family.blade.php */ ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Area Famiglie — Altramusica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
@unless(request()->routeIs('family.login'))
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('family.dashboard') }}">Area Famiglie</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#famNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="famNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('family.dashboard') ? 'active' : '' }}" href="{{ route('family.dashboard') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('family.documents*') ? 'active' : '' }}" href="{{ route('family.documents') }}">Documenti</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('family.receipts*') ? 'active' : '' }}" href="{{ route('family.receipts') }}">Ricevute</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('family.communications*') ? 'active' : '' }}" href="{{ route('family.communications') }}">Comunicazioni</a>
                    </li>
                </ul>
                <form method="POST" action="{{ route('family.logout') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-light btn-sm">Esci</button>
                </form>
            </div>
        </div>
    </nav>
@endunless

@yield('family-content')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
