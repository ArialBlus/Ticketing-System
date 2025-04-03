<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
        🔔 Notificaciones <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
    </a>
    <ul class="dropdown-menu">
        @foreach (auth()->user()->unreadNotifications as $notification)
            <li>
                <a class="dropdown-item" href="{{ url('/tickets/' . $notification->data['ticket_id']) }}">
                    {{ $notification->data['title'] }} - {{ $notification->data['status'] }}
                </a>
            </li>
        @endforeach
    </ul>
</li>
<ul class="navbar-nav ms-auto">
    @guest
        <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">Ingresar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
        </li>
    @else
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
        </li>
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">Salir</button>
            </form>
        </li>
    @endguest
</ul>
