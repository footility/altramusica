@php
    $sections = [
        [
            'label' => null,
            'items' => [
                [
                    'label' => 'Dashboard',
                    'icon' => 'bi-house-door',
                    'route' => 'admin.dashboard',
                    'active' => request()->routeIs('admin.dashboard'),
                ],
            ],
        ],
        [
            'label' => 'Anagrafiche',
            'items' => [
                [
                    'label' => 'Anni Scolastici',
                    'icon' => 'bi-calendar-range',
                    'route' => 'admin.academic-years.index',
                    'active' => request()->routeIs('admin.academic-years.*'),
                ],
                [
                    'label' => 'Studenti',
                    'icon' => 'bi-people',
                    'route' => 'admin.students.index',
                    'active' => request()->routeIs('admin.students.*'),
                ],
                [
                    'label' => 'Genitori/Tutori',
                    'icon' => 'bi-person-badge',
                    'route' => 'admin.guardians.index',
                    'active' => request()->routeIs('admin.guardians.*'),
                ],
                [
                    'label' => 'Docenti',
                    'icon' => 'bi-person-workspace',
                    'route' => 'admin.teachers.index',
                    'active' => request()->routeIs('admin.teachers.*'),
                ],
                [
                    'label' => 'Aule',
                    'icon' => 'bi-door-open',
                    'route' => 'admin.classrooms.index',
                    'active' => request()->routeIs('admin.classrooms.*'),
                ],
            ],
        ],
        [
            'label' => 'Didattica',
            'items' => [
                [
                    'label' => 'Corsi',
                    'icon' => 'bi-book',
                    'route' => 'admin.courses.index',
                    'active' => request()->routeIs('admin.courses.*'),
                ],
                [
                    'label' => 'Iscrizioni',
                    'icon' => 'bi-clipboard-check',
                    'route' => 'admin.enrollments.index',
                    'active' => request()->routeIs('admin.enrollments.*'),
                ],
                [
                    'label' => 'Calendario Base',
                    'icon' => 'bi-calendar3',
                    'route' => 'admin.calendar.index',
                    'active' => request()->routeIs('admin.calendar.*'),
                ],
                [
                    'label' => 'Calendario Lezioni',
                    'icon' => 'bi-calendar-event',
                    'route' => 'admin.lessons.calendar',
                    'active' => request()->routeIs('admin.lessons.calendar*'),
                ],
                [
                    'label' => 'Esami',
                    'icon' => 'bi-clipboard-data',
                    'route' => 'admin.exams.index',
                    'active' => request()->routeIs('admin.exams.*'),
                ],
                [
                    'label' => 'Libri',
                    'icon' => 'bi-journal-bookmark',
                    'route' => 'admin.books.index',
                    'active' => request()->routeIs('admin.books.*'),
                ],
                [
                    'label' => 'Distribuzioni Libri',
                    'icon' => 'bi-journal-arrow-up',
                    'route' => 'admin.book-distributions.index',
                    'active' => request()->routeIs('admin.book-distributions.*'),
                ],
                [
                    'label' => 'Disponibilità Studenti',
                    'icon' => 'bi-clock',
                    'route' => 'admin.student-availability.index',
                    'active' => request()->routeIs('admin.student-availability.*'),
                ],
                [
                    'label' => 'Disponibilità Docenti',
                    'icon' => 'bi-clock-history',
                    'route' => 'admin.teacher-availability.index',
                    'active' => request()->routeIs('admin.teacher-availability.*'),
                ],
            ],
        ],
        [
            'label' => 'Amministrazione',
            'items' => [
                [
                    'label' => 'Contratti',
                    'icon' => 'bi-file-earmark-text',
                    'route' => 'admin.contracts.index',
                    'active' => request()->routeIs('admin.contracts.*'),
                ],
                [
                    'label' => 'Fatture',
                    'icon' => 'bi-receipt',
                    'route' => 'admin.invoices.index',
                    'active' => request()->routeIs('admin.invoices.*'),
                ],
                [
                    'label' => 'Scadenzario Rate',
                    'icon' => 'bi-calendar-check',
                    'route' => 'admin.payment-plans.index',
                    'active' => request()->routeIs('admin.payment-plans.*'),
                ],
                [
                    'label' => 'Conto Orario',
                    'icon' => 'bi-clock-history',
                    'route' => 'admin.teacher-hours.index',
                    'active' => request()->routeIs('admin.teacher-hours.*'),
                ],
                [
                    'label' => 'Strumenti',
                    'icon' => 'bi-music-note',
                    'route' => 'admin.instruments.index',
                    'active' => request()->routeIs('admin.instruments.*'),
                ],
                [
                    'label' => 'Noleggi Strumenti',
                    'icon' => 'bi-box-seam',
                    'route' => 'admin.instrument-rentals.index',
                    'active' => request()->routeIs('admin.instrument-rentals.*'),
                ],
                [
                    'label' => 'Documenti',
                    'icon' => 'bi-folder2-open',
                    'route' => 'admin.documents.index',
                    'active' => request()->routeIs('admin.documents.*'),
                ],
            ],
        ],
        [
            'label' => 'Extra (non AS-IS)',
            'items' => [
                [
                    'label' => 'Primi Contatti',
                    'icon' => 'bi-envelope-heart',
                    'route' => 'admin.first-contacts.index',
                    'active' => request()->routeIs('admin.first-contacts.*'),
                ],
                [
                    'label' => 'Proposte Orarie',
                    'icon' => 'bi-calendar-check',
                    'route' => 'admin.schedule-proposals.index',
                    'active' => request()->routeIs('admin.schedule-proposals.*'),
                ],
                [
                    'label' => 'Comunicazioni',
                    'icon' => 'bi-envelope',
                    'route' => 'admin.communications.index',
                    'active' => request()->routeIs('admin.communications.*'),
                ],
                [
                    'label' => 'Attività Extra',
                    'icon' => 'bi-music-note-beamed',
                    'route' => 'admin.extra-activities.index',
                    'active' => request()->routeIs('admin.extra-activities.*'),
                ],
            ],
        ],
    ];
@endphp

<nav class="admin-sidebar">
    <div class="admin-sidebar__header">
        <div class="d-flex align-items-center gap-2">
            <span class="admin-sidebar__logo">LM</span>
            <div class="min-w-0">
                <div class="admin-sidebar__brand">L'Altramusica</div>
                <div class="admin-sidebar__subtitle">Gestionale</div>
            </div>
        </div>
    </div>

    <div class="admin-sidebar__content">
        <ul class="nav nav-pills flex-column gap-1">
            @foreach($sections as $section)
                @if($section['label'])
                    <li class="nav-item mt-3">
                        <div class="admin-nav-section">{{ $section['label'] }}</div>
                    </li>
                @endif

                @foreach($section['items'] as $item)
                    <li class="nav-item">
                        <a
                            class="nav-link d-flex align-items-center gap-2 {{ $item['active'] ? 'active' : '' }}"
                            href="{{ route($item['route']) }}"
                        >
                            <i class="bi {{ $item['icon'] }}"></i>
                            <span class="text-truncate">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            @endforeach
        </ul>
    </div>
</nav>

