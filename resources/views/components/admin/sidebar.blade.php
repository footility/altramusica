@php
    $areas = config('admin_navigation');
    $activeArea = collect($areas)->first(function (array $area) {
        return collect($area['patterns'])->contains(fn (string $pattern) => request()->routeIs($pattern));
    }) ?? $areas['dashboard'];
@endphp

<nav class="admin-sidebar" aria-label="Navigazione area {{ $activeArea['label'] }}">
    <div class="admin-sidebar__header">
        <div class="admin-nav-section">Area corrente</div>
        <div class="admin-sidebar__area">
            <i class="bi {{ $activeArea['icon'] }}"></i>
            <span>{{ $activeArea['label'] }}</span>
        </div>
    </div>

    <div class="admin-sidebar__content">
        <ul class="nav nav-pills flex-column gap-1">
            @foreach($activeArea['items'] as $item)
                @continue(!empty($item['permission']) && !auth()->user()->can($item['permission']))
                @php
                    $isActive = collect($item['patterns'])->contains(fn (string $pattern) => request()->routeIs($pattern));
                @endphp
                <li class="nav-item">
                    <a
                        class="nav-link d-flex align-items-center gap-2 {{ $isActive ? 'active' : '' }}"
                        href="{{ route($item['route']) }}"
                    >
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span class="text-truncate">{{ $item['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</nav>
