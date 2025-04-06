<!-- resources/views/categories/index.blade.php -->


<x-app-layout>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Categorías</h3>
            <div class="d-flex gap-3">
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus "></i> Nueva Categoría
                </a>
                <form action="{{ route('categories.index') }}" method="GET" class="d-flex align-items-center">
                    <select name="category" class="form-select form-select-sm me-2">
                        <option value="">Todas las categorías</option>
                        @foreach($allCategories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Tickets</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description }}</td>
                        <td>{{ $category->tickets_count }}</td>
                        <td>
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>


