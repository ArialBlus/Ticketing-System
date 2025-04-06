<x-app-layout>
    <div class="container-fluid">
        <!-- Título y Botón de Nuevo Ticket -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
            <h2 class="mb-2 mb-md-0">Tickets</h2>
            @can('crear-ticket')
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i>Nuevo Ticket
            </a>
            @endcan
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('tickets.index') }}" method="GET" class="row g-3 row-cols-1 row-cols-sm-2 row-cols-md-4">
                    <div class="col">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ request('status') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label class="form-label">Categoría</label>
                        <select name="category" class="form-select">
                            <option value="">Todas</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Título o descripción...">
                    </div>
                    <div class="col d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Tickets -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Título</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Creado por</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr class="ticket-priority-{{ $ticket->priority }}">
                                    <td data-label="#">{{ $ticket->id }}</td>
                                    <td data-label="Título">
                                        <a href="{{ route('tickets.show', $ticket->id) }}" class="text-decoration-none">
                                            {{ $ticket->title }}
                                        </a>
                                    </td>
                                    <td data-label="Categoría">
                                        <span class="badge bg-info">{{ $ticket->category->name }}</span>
                                    </td>
                                    <td data-label="Estado">
                                        <span class="badge status-badge bg-{{ $ticket->status->name == 'Abierto' ? 'success' : ($ticket->status->name == 'En Proceso' ? 'warning' : 'secondary') }}">
                                            {{ $ticket->status->name }}
                                        </span>
                                    </td>
                                    <td data-label="Creado por">{{ $ticket->user->name }}</td>
                                    <td data-label="Fecha">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                    <td data-label="Acciones">
                                    <div class="btn-group">
                                        <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @canany(['editar-ticket-asignado', 'editar-todos-tickets'])
                                        <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-sm btn-warning ms-2">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                    </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                                            <p class="mb-0">No hay tickets disponibles</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
            .table thead {
                display: none;
            }
            .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }
            .table tr {
                margin-bottom: 10px;
                border-bottom: 2px solid #ddd;
            }
            .table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                width: 45%;
                text-align: left;
                font-weight: bold;
            }
        }
    </style>
</x-app-layout>
