<nav class="admin-topbar navbar navbar-expand-lg bg-white border-bottom">
    <div class="container-fluid">
        <div class="d-flex align-items-center gap-2">
            <div class="admin-topbar__title">
                {{ $pageTitle ?? 'Dashboard' }}
            </div>
        </div>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                    {{ Auth::user()->name ?? 'Utente' }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                </ul>
            </li>
        </ul>
    </div>
</nav>

