<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssessmentCategory;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssessmentCategoryExport;

class AssessmentCategoryController extends Controller
{
    public function index()
    {
        return view('assessment-categories.index');
    }

    public function data()
    {
        return AssessmentCategory::latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        AssessmentCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ? true : false
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return AssessmentCategory::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = AssessmentCategory::findOrFail($id);

        $request->validate([
            'name' => 'required'
        ]);

        $data->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ? true : false
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        AssessmentCategory::destroy($id);
        return response()->json(['success' => true]);
    }

    public function exportExcel()
    {
        return Excel::download(new AssessmentCategoryExport, 'data-assessment-categories-' . date('Ymd') . '.xlsx');
    }
}
