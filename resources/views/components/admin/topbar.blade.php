@php
    $areas = config('admin_navigation');
@endphp

<header class="admin-topbar">
    <a class="admin-brand" href="{{ route('admin.dashboard') }}" aria-label="L'Altramusica - gestionale">
        <span class="admin-brand__logo">LM</span>
        <span class="admin-brand__text">
            <strong>L'Altramusica</strong>
            <small>Gestionale</small>
        </span>
    </a>

    <nav class="admin-areas" aria-label="Aree principali">
        @foreach($areas as $area)
            @continue(!empty($area['permission']) && !auth()->user()->can($area['permission']))
            @php
                $isActive = collect($area['patterns'])->contains(fn (string $pattern) => request()->routeIs($pattern));
            @endphp
            <a class="admin-area-link {{ $isActive ? 'active' : '' }}" href="{{ route($area['home_route']) }}">
                <i class="bi {{ $area['icon'] }}"></i>
                <span>{{ $area['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="admin-account dropdown">
        <button class="btn admin-account__button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i>
            <span>{{ Auth::user()->name ?? 'Utente' }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li class="px-3 pt-2 pb-1 small text-muted">{{ $pageTitle ?? 'Dashboard' }}</li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Logout
                </a>
            </li>
        </ul>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
    </div>
</header>
