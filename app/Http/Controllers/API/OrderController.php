<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/orders",
     *     summary="Get all orders",
     *     description="Retrieve all orders with optional filtering and pagination",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="payment_id",
     *         in="query",
     *         required=false,
     *         description="Filter by payment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="cart_id",
     *         in="query",
     *         required=false,
     *         description="Filter by cart ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         required=false,
     *         description="Filter by order date (from)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         required=false,
     *         description="Filter by order date (to)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page for pagination",
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Order::with(['cart.user', 'cart.product', 'payment']);

        if ($request->has('payment_id')) {
            $query->where('payment_id', $request->payment_id);
        }

        if ($request->has('cart_id')) {
            $query->where('cart_id', $request->cart_id);
        }

        if ($request->has('from_date')) {
            $query->where('order_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('order_date', '<=', $request->to_date);
        }

        $perPage = $request->per_page ?? 15;
        $orders = $query->paginate($perPage);

        return response()->json([
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total()
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/orders",
     *     summary="Create a new order",
     *     description="Create a new order with validation of related cart and payment entities",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"final_price", "cart_id", "payment_id", "order_date"},
     *             @OA\Property(property="final_price", type="number", example=150000),
     *             @OA\Property(property="cart_id", type="integer", example=1),
     *             @OA\Property(property="payment_id", type="integer", example=1),
     *             @OA\Property(property="order_date", type="string", format="date", example="2023-01-15")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order created successfully"),
     *             @OA\Property(property="order", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart or Payment not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Cart already has an order"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'final_price' => 'required|numeric|min:0',
            'cart_id' => 'required|exists:carts,cart_id',
            'payment_id' => 'required|exists:payments,payment_id',
            'order_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cart = Cart::findOrFail($request->cart_id);

            if ($cart->order) {
                return response()->json([
                    'message' => 'This cart already has an associated order'
                ], 409);
            }

            $payment = Payment::findOrFail($request->payment_id);

            $order = Order::create([
                'final_price' => $request->final_price,
                'cart_id' => $request->cart_id,
                'payment_id' => $request->payment_id,
                'order_date' => $request->order_date
            ]);

            $order->load(['cart.user', 'cart.product', 'payment']);

            return response()->json([
                'message' => 'Order created successfully',
                'order' => $order
            ], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Cart or Payment not found'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/orders/{id}",
     *     summary="Get an order by ID",
     *     description="Retrieve detailed order information by ID with related entities",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order found",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $order = Order::with(['cart.user', 'cart.product', 'payment'])->findOrFail($id);
            return response()->json($order, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/orders/{id}",
     *     summary="Update an order",
     *     description="Update order details with validation of related entities",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="final_price", type="number", example=160000),
     *             @OA\Property(property="cart_id", type="integer", example=1),
     *             @OA\Property(property="payment_id", type="integer", example=1),
     *             @OA\Property(property="order_date", type="string", format="date", example="2023-01-16")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order updated successfully"),
     *             @OA\Property(property="order", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'final_price' => 'sometimes|numeric|min:0',
                'cart_id' => 'sometimes|exists:carts,cart_id',
                'payment_id' => 'sometimes|exists:payments,payment_id',
                'order_date' => 'sometimes|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if ($request->has('cart_id') && $request->cart_id != $order->cart_id) {
                $cartOrder = Order::where('cart_id', $request->cart_id)
                                  ->where('order_id', '!=', $id)
                                  ->first();

                if ($cartOrder) {
                    return response()->json([
                        'message' => 'This cart already has an associated order'
                    ], 409);
                }
            }

            $order->update($request->all());

            $order->load(['cart.user', 'cart.product', 'payment']);

            return response()->json([
                'message' => 'Order updated successfully',
                'order' => $order
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/orders/{id}",
     *     summary="Delete an order",
     *     description="Delete an existing order",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Order ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();
            return response()->json(['message' => 'Order deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{user_id}/orders",
     *     summary="Get user's orders",
     *     description="Retrieve all orders for a specific user",
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved orders",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Order"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function getUserOrders($userId)
    {
        // Check if user exists
        if (!\App\Models\User::where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Find carts belonging to this user
        $cartIds = Cart::where('user_id', $userId)->pluck('cart_id');

        // Find orders for these carts
        $orders = Order::with(['cart.user', 'cart.product', 'payment'])
                      ->whereIn('cart_id', $cartIds)
                      ->get();

        return response()->json($orders, 200);
    }
}
