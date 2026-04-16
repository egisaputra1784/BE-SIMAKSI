<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointRule;

class PointRuleController extends Controller
{
    public function index()
    {
        return view('point-rules.index');
    }

    public function data()
    {
        return PointRule::latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'rule_name' => 'required',
            'target_role' => 'required',
            'condition_type' => 'required|in:TIME,ALPHA',
            'min_value' => 'nullable|integer',
            'max_value' => 'nullable|integer',
            'point_modifier' => 'required|integer',
        ]);

        PointRule::create([
            'rule_name' => $request->rule_name,
            'target_role' => $request->target_role,
            'condition_type' => $request->condition_type,
            'min_value' => $request->min_value,
            'max_value' => $request->max_value,
            'point_modifier' => $request->point_modifier,
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return PointRule::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $data = PointRule::findOrFail($id);

        $data->update([
            'rule_name' => $request->rule_name,
            'target_role' => $request->target_role,
            'condition_type' => $request->condition_type,
            'min_value' => $request->min_value,
            'max_value' => $request->max_value,
            'point_modifier' => $request->point_modifier,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        PointRule::destroy($id);
        return response()->json(['success' => true]);
    }
}
