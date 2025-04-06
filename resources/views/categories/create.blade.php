<!-- resources/views/categories/create.blade.php -->

<x-app-layout>
<div class="card">
    <div class="card-header">
        <h3>Crear Nueva Categoría</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Crear Categoría</button>
        </form>
    </div>
</div>
</x-app-layout>
