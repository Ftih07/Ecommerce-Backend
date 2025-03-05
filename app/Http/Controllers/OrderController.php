<?php

namespace App\Http\Controllers;

use App\Models\Order; 
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        // Contoh: load cart & payment
        $orders = Order::with(['cart', 'payment'])->get();
        return response()->json($orders, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'final_price' => 'required|numeric',
            'cart_id' => 'required|exists:carts,cart_id',
            'payment_id' => 'required|exists:payments,payment_id',
            'order_date' => 'required|date',
        ]);

        $order = Order::create($request->all());
        return response()->json($order, 201);
    }

    public function show($id)
    {
        $order = Order::with(['cart', 'payment'])->find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json($order, 200);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $request->validate([
            'final_price' => 'sometimes|numeric',
            'cart_id' => 'sometimes|exists:carts,cart_id',
            'payment_id' => 'sometimes|exists:payments,payment_id',
            'order_date' => 'sometimes|date',
        ]);

        $order->update($request->all());
        return response()->json($order, 200);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        $order->delete();
        return response()->json(['message' => 'Order deleted'], 200);
    }
}
