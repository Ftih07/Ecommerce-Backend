<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        return response()->json(Store::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'city' => 'required|string',
            'profile_image' => 'nullable|string',
        ]);

        $store = Store::create($request->all());
        return response()->json($store, 201);
    }

    public function show($id)
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }
        return response()->json($store, 200);
    }

    public function update(Request $request, $id)
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }

        $store->update($request->all());
        return response()->json($store, 200);
    }

    public function destroy($id)
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }
        $store->delete();
        return response()->json(['message' => 'Store deleted'], 200);
    }
}
