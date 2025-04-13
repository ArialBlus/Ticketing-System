<x-app-layout>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Detalles del Ticket -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="card-title mb-0">{{ $ticket->title }}</h2>
                        <span class="badge bg-{{ $ticket->status->name == 'Abierto' ? 'success' : ($ticket->status->name == 'En Proceso' ? 'warning' : 'secondary') }} status-badge">
                            {{ $ticket->status->name }}
                        </span>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Categoría:</strong> 
                                <span class="badge bg-info">{{ $ticket->category->name }}</span>
                            </p>
                            <p class="mb-1"><strong>Creado por:</strong> {{ $ticket->user->name }}</p>
                            <p class="mb-1"><strong>Fecha:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Asignado a:</strong> 
                                {{ $ticket->assigned_to ? $ticket->assignedTo->name : 'Sin asignar' }}
                            </p>
                            <p class="mb-1"><strong>Última actualización:</strong> 
                                {{ $ticket->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <div class="ticket-description mb-4">
                        <h5>Descripción</h5>
                        <div class="p-3 bg-light rounded">
                            {{ $ticket->description }}
                        </div>
                    </div>

                    @canany(['editar-todos-tickets', 'editar-ticket-asignado'])
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Cambiar Estado</h5>
                        <form action="{{ route('tickets.changeStatus', $ticket->id) }}" method="POST" class="d-flex gap-2">
                            @csrf
                            @method('PUT')
                            <select name="status_id" class="form-select" required>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}" {{ $ticket->status_id == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Actualizar
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>

            <!-- Sección de Comentarios -->
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-4">Comentarios ({{ $ticket->comments->count() }})</h4>

                    <div class="comments-section mb-4">
                        @forelse ($ticket->comments as $comment)
                            <div class="comment mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar bg-light rounded-circle p-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $comment->user->name }}</h6>
                                            <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <p class="mb-0 mt-2">{{ $comment->message }}</p>
                                    </div>
                                </div>
                            </div>
                            @if (!$loop->last)
                                <hr class="my-3">
                            @endif
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-comments fa-2x mb-2"></i>
                                <p class="mb-0">No hay comentarios aún</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Formulario de Comentarios -->
                    <form action="{{ route('comments.store', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="message" class="form-label">Agregar un comentario</label>
                            <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror" 
                                      rows="3" required></textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Enviar comentario
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar derecho con acciones -->
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Acciones</h5>
                    <div class="d-grid gap-2">
                        @canany(['editar-todos-tickets', 'editar-tickets-asignado'])
                        <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Editar Ticket
                        </a>
                        @endcan
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver a Tickets
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
