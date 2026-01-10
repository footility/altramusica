<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Gestionale L\'Altramusica')</title>
    
    <!-- Bootstrap CSS -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="bg-dark text-white vh-100 position-fixed" style="width: 250px; padding-top: 20px;">
            <div class="px-3">
                <h4 class="text-white mb-4">L'Altramusica</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-primary' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-house-door"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.academic-years.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.academic-years.index') }}">
                            <i class="bi bi-calendar-range"></i> Anni Scolastici
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.calendar.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.calendar.index') }}">
                            <i class="bi bi-calendar3"></i> Calendario Base
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.lessons.calendar.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.lessons.calendar') }}">
                            <i class="bi bi-calendar-event"></i> Calendario Lezioni
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.first-contacts.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.first-contacts.index') }}">
                            <i class="bi bi-envelope-heart"></i> Primi Contatti
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.schedule-proposals.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.schedule-proposals.index') }}">
                            <i class="bi bi-calendar-check"></i> Proposte Orarie
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.communications.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.communications.index') }}">
                            <i class="bi bi-envelope"></i> Comunicazioni
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.teacher-hours.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.teacher-hours.index') }}">
                            <i class="bi bi-clock-history"></i> Conto Orario
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.extra-activities.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.extra-activities.index') }}">
                            <i class="bi bi-music-note-beamed"></i> Attività Extra
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.classrooms.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.classrooms.index') }}">
                            <i class="bi bi-door-open"></i> Aule
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.students.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.students.index') }}">
                            <i class="bi bi-people"></i> Studenti
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.guardians.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.guardians.index') }}">
                            <i class="bi bi-person-badge"></i> Genitori/Tutori
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.teachers.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.teachers.index') }}">
                            <i class="bi bi-person-workspace"></i> Docenti
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.student-availability.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.student-availability.index') }}">
                            <i class="bi bi-clock-history"></i> Disponibilità Studenti
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.teacher-availability.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.teacher-availability.index') }}">
                            <i class="bi bi-clock-history"></i> Disponibilità Docenti
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.courses.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.courses.index') }}">
                            <i class="bi bi-book"></i> Corsi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.enrollments.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.enrollments.index') }}">
                            <i class="bi bi-clipboard-check"></i> Iscrizioni
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.invoices.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.invoices.index') }}">
                            <i class="bi bi-receipt"></i> Fatture
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.instruments.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.instruments.index') }}">
                            <i class="bi bi-music-note"></i> Strumenti
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.contracts.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.contracts.index') }}">
                            <i class="bi bi-file-earmark-text"></i> Contratti
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.exams.*') ? 'active bg-primary' : '' }}" href="{{ route('admin.exams.index') }}">
                            <i class="bi bi-clipboard-data"></i> Esami
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="flex-grow-1" style="margin-left: 250px;">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                <div class="container-fluid px-4">
                    <span class="navbar-text">
                        @yield('page-title', 'Dashboard')
                    </span>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name ?? 'Utente' }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

