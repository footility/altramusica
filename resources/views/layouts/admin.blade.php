<x-admin.layout>
    <x-slot:title>
        @yield('title', 'Gestionale L\'Altramusica')
    </x-slot:title>

    <x-slot:pageTitle>
        @yield('page-title', 'Dashboard')
    </x-slot:pageTitle>

    @yield('content')
</x-admin.layout>

