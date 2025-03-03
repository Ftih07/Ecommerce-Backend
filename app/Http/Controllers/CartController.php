<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        // Contoh: load user & product
        $carts = Cart::with(['user', 'product'])->get();
        return response()->json($carts, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'total_price' => 'required|numeric',
            'product_id' => 'required|exists:products,product_id',
            'user_id' => 'required|exists:users,user_id',
        ]);

        $cart = Cart::create($request->all());
        return response()->json($cart, 201);
    }

    public function show($id)
    {
        $cart = Cart::with(['user', 'product'])->find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        return response()->json($cart, 200);
    }

    public function update(Request $request, $id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $request->validate([
            'quantity' => 'integer',
            'total_price' => 'numeric',
            'product_id' => 'exists:products,product_id',
            'user_id' => 'exists:users,user_id',
        ]);

        $cart->update($request->all());
        return response()->json($cart, 200);
    }

    public function destroy($id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        $cart->delete();
        return response()->json(['message' => 'Cart deleted'], 200);
    }
}
