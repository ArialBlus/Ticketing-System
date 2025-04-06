<x-app-layout>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Editar Categoría</h3>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        <div class="card-body">
            <!-- Formulario para editar -->
            <form action="{{ route('categories.update', $category) }}" method="POST" class="mb-3">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </form>

            <!-- Formulario separado para eliminar -->
            <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash "></i> Eliminar
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
