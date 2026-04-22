<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlexibilityItem;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\FlexibilityItemExport;

class FlexibilityItemController extends Controller
{
    public function index()
    {
        return view('flexibility-items.index');
    }

    public function data()
    {
        return FlexibilityItem::latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required',
            'point_cost' => 'required|integer',
            'type' => 'required|in:LATE,ALPHA',
            'stock_limit' => 'nullable|integer',
            'max_late_minutes' => 'nullable|integer'
        ]);

        FlexibilityItem::create([
            'item_name' => $request->item_name,
            'point_cost' => $request->point_cost,
            'type' => $request->type,
            'stock_limit' => $request->stock_limit,
            'max_late_minutes' => $request->max_late_minutes
        ]);

        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        return FlexibilityItem::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $item = FlexibilityItem::findOrFail($id);

        $item->update([
            'item_name' => $request->item_name,
            'point_cost' => $request->point_cost,
            'type' => $request->type,
            'stock_limit' => $request->stock_limit,
            'max_late_minutes' => $request->max_late_minutes
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        FlexibilityItem::destroy($id);
        return response()->json(['success' => true]);
    }

    public function exportExcel()
    {
        return Excel::download(new FlexibilityItemExport, 'data-flexibility-items-' . date('Ymd') . '.xlsx');
    }
}
