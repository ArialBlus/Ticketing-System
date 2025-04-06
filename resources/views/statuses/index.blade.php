<x-app-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="card-title mb-0">Estados de Tickets</h2>
                        @can('gestionar tickets')
                            <a href="{{ route('statuses.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Nuevo Estado
                            </a>
                        @endcan
                    </div>
                    <div class="card-body">
                        <!-- Mostrar mensajes de éxito o error -->
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th>Tickets Totales</th>
                                        <th>Tickets Abiertos</th>
                                        <th>Tickets Cerrados</th>
                                        <th>Último Cambio</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statuses as $status)
                                        <tr>
                                            <td>
                                                <span class="badge bg-{{ $status->name == 'Abierto' ? 'success' : ($status->name == 'En Proceso' ? 'warning' : 'secondary') }}">
                                                    {{ $status->name }}
                                                </span>
                                            </td>
                                            <td>{{ $status->tickets_count }}</td>
                                            <td>{{ $status->tickets()->where('status_id', $status->id)->where('status_id', '!=', 3)->count() }}</td>
                                            <td>{{ $status->tickets()->where('status_id', 3)->count() }}</td>
                                            <td>
                                                @if($status->tickets()->orderBy('updated_at', 'desc')->first())
                                                    {{ $status->tickets()->orderBy('updated_at', 'desc')->first()->updated_at->diffForHumans() }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('statuses.edit', $status->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('statuses.destroy', $status->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este estado?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>