<div class="d-flex flex-column p-3">
    <!-- Logo/Brand -->
    <div class="text-center mb-4">
        <h4 class="text-white mb-0">Sistema de Tickets</h4>
    </div>

    <!-- Navigation -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>

     
            <!--ver tickets-->
            @canany(['ver-tickets-propios', 'ver-todos-tickets'])
            <li class="nav-item">
                <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt"></i> Tickets
                </a>
            </li>
            @endcan

            @can('crear-ticket')
            <li class="nav-item">
                <a href="{{ route('tickets.create') }}" class="nav-link {{ request()->routeIs('tickets.create') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle"></i> Crear Ticket
                </a>
            </li>
            @endcan

            @can('gestionar-usuarios')
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Usuarios
                </a>
            </li>
            @endcan

            @can('gestionar-usuarios')
            <li class="nav-item">
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> Categorías
                </a>
            </li>
            @endcan

            @can('gestionar-usuarios')
            <li class="nav-item">
                <a href="{{ route('statuses.index') }}" class="nav-link {{ request()->routeIs('statuses.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i> Estados
                </a>
            </li>
            @endcan

            <!-- Profile link at bottom -->
            <li class="nav-item mt-auto">
                <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i> Mi Perfil
                </a>
            </li>
        </ul>
    </div>
