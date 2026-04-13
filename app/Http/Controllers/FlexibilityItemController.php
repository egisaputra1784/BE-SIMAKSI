<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlexibilityItem;

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
            'stock_limit' => 'nullable|integer'
        ]);

        FlexibilityItem::create([
            'item_name' => $request->item_name,
            'point_cost' => $request->point_cost,
            'stock_limit' => $request->stock_limit
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
            'stock_limit' => $request->stock_limit
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        FlexibilityItem::destroy($id);
        return response()->json(['success' => true]);
    }
}
