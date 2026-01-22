<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trim(strip_tags($title ?? 'Gestionale L\'Altramusica')) }}</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    @stack('styles')
</head>
<body class="admin-body">
    <div class="admin-shell">
        <x-admin.sidebar />

        <div class="admin-main">
            <x-admin.topbar>
                <x-slot:pageTitle>
                    {{ $pageTitle ?? 'Dashboard' }}
                </x-slot:pageTitle>
            </x-admin.topbar>

            <main class="admin-content container-fluid py-4">
                <x-admin.alerts />

                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

