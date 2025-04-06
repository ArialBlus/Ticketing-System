<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
        {
            $allCategories = Category::all(); // Para el select del filtro

            $query = Category::withCount('tickets');

            // Filtro por nombre
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Filtro por ID de categoría
            if ($request->filled('category')) {
                $query->where('id', $request->category);
            }

            $categories = $query->get();

            return view('categories.index', compact('categories', 'allCategories'));
        }
    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories',
        ]);

        Category::create($request->all());
        return redirect()->route('categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
        ]);

        $category->update($request->all());
        return redirect()->route('categories.index')->with('success', 'Categoría actualizada exitosamente.');
    }



    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Categoría eliminada exitosamente.');
    }
}
