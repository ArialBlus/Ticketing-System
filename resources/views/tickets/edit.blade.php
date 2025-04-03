<x-app-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="card-title mb-0">Editar Ticket</h2>
                            <span class="badge bg-{{ $ticket->status->name == 'Abierto' ? 'success' : ($ticket->status->name == 'En Proceso' ? 'warning' : 'secondary') }} status-badge">
                                {{ $ticket->status->name }}
                            </span>
                        </div>

                        <form action="{{ route('tickets.update', $ticket->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Título -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Título</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $ticket->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Categoría -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Categoría</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" name="category_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id', $ticket->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Estado -->
                            @can('gestionar tickets')
                            <div class="mb-3">
                                <label for="status_id" class="form-label">Estado</label>
                                <select class="form-select @error('status_id') is-invalid @enderror" 
                                        id="status_id" name="status_id" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" 
                                            {{ old('status_id', $ticket->status_id) == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            @endcan

                            <!-- Prioridad -->
                            <div class="mb-3">
                                <label for="priority" class="form-label">Prioridad</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority" required>
                                    <option value="low" {{ old('priority', $ticket->priority) == 'low' ? 'selected' : '' }}>Baja</option>
                                    <option value="medium" {{ old('priority', $ticket->priority) == 'medium' ? 'selected' : '' }}>Media</option>
                                    <option value="high" {{ old('priority', $ticket->priority) == 'high' ? 'selected' : '' }}>Alta</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="mb-4">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="5" required>{{ old('description', $ticket->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Información adicional -->
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Creado por:</strong> {{ $ticket->user->name }}</p>
                                        <p class="mb-1"><strong>Fecha de creación:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Última actualización:</strong> {{ $ticket->updated_at->diffForHumans() }}</p>
                                        <p class="mb-1"><strong>Asignado a:</strong> {{ $ticket->assigned_to ? $ticket->assignedTo->name : 'Sin asignar' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Volver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
