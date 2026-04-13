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
            'condition_operator' => 'required',
            'condition_value' => 'required',
            'point_modifier' => 'required|integer',
        ]);

        PointRule::create([
            'rule_name' => $request->rule_name,
            'target_role' => $request->target_role,
            'condition_operator' => $request->condition_operator,
            'condition_value' => $request->condition_value,
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
            'condition_operator' => $request->condition_operator,
            'condition_value' => $request->condition_value,
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
