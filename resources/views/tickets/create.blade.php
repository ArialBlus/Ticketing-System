<x-app-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Crear Nuevo Ticket</h2>

                        <form action="{{ route('tickets.store') }}" method="POST">
                            @csrf

                            <!-- Título -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Título</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required>
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
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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

                            <!-- Prioridad -->
                            <div class="mb-3">
                                <label for="priority" class="form-label">Prioridad</label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" name="priority" required>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Baja</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Media</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Alta</option>
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
                                          id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Archivos adjuntos (si se implementa) -->
                            <div class="mb-4">
                                <label for="attachments" class="form-label">Archivos Adjuntos (opcional)</label>
                                <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                                <div class="form-text">Puedes adjuntar múltiples archivos. Tamaño máximo: 2MB por archivo.</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Volver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Crear Ticket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
