<?php

namespace App\Http\Controllers;

use App\Models\AssessmentCategory;
use Illuminate\Http\Request;

class KategoriControllerController extends Controller
{
    // List semua kategori
    public function index()
    {
        $categories = AssessmentCategory::all();
        return response()->json($categories);
    }

    // Lihat 1 kategori
    public function show($id)
    {
        $category = AssessmentCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        return response()->json($category);
    }

    // Buat kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $category = AssessmentCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json($category, 201);
    }

    // Update kategori
    public function update(Request $request, $id)
    {
        $category = AssessmentCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ]);

        $category->update($request->only(['name', 'description', 'is_active']));

        return response()->json($category);
    }

    // Hapus kategori
    public function destroy($id)
    {
        $category = AssessmentCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}
