<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Get list of orders",
     *     tags={"Order"},
     *     @OA\Response(response="200", description="List of orders")
     * )
     */

    public function index()
    {
        // Contoh: load cart & payment
        $orders = Order::with(['cart', 'payment'])->get();
        return response()->json($orders, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create new order",
     *     tags={"Order"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"final_price","cart_id","payment_id","order_date"},
     *             @OA\Property(property="final_price", type="number"),
     *             @OA\Property(property="cart_id", type="integer"),
     *             @OA\Property(property="payment_id", type="integer"),
     *             @OA\Property(property="order_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Order created")
     * )
     */

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
    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get specific order",
     *     tags={"Order"},
     *     @OA\Parameter(
     *         name="id", in="path", required=true, @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Order detail"),
     *     @OA\Response(response=404, description="Order not found")
     * )
     */
    public function show($id)
    {
        $order = Order::with(['cart', 'payment'])->find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }
        return response()->json($order, 200);
    }
    /**
     * @OA\Put(
     *     path="/api/orders/{id}",
     *     summary="Update specific order",
     *     tags={"Order"},
     *     @OA\Parameter(
     *         name="id", in="path", required=true, @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="final_price", type="number"),
     *             @OA\Property(property="cart_id", type="integer"),
     *             @OA\Property(property="payment_id", type="integer"),
     *             @OA\Property(property="order_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Order updated"),
     *     @OA\Response(response=404, description="Order not found")
     * )
     */
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
    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Delete specific order",
     *     tags={"Order"},
     *     @OA\Parameter(
     *         name="id", in="path", required=true, @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Order deleted"),
     *     @OA\Response(response=404, description="Order not found")
     * )
     */
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
