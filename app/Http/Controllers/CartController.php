<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/carts",
     *     summary="Get all carts",
     *     tags={"Carts"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Cart"))
     *     )
     * )
     */

    public function index()
    {
        // Contoh: load user & product
        $carts = Cart::with(['user', 'product'])->get();
        return response()->json($carts, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/carts",
     *     summary="Create a new cart",
     *     tags={"Carts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity", "total_price", "product_id", "user_id"},
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="total_price", type="number", format="float"),
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="user_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cart created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Cart")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

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

    /**
     * @OA\Get(
     *     path="/api/carts/{id}",
     *     summary="Get a cart by ID",
     *     tags={"Carts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart found",
     *         @OA\JsonContent(ref="#/components/schemas/Cart")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found"
     *     )
     * )
     */

    public function show($id)
    {
        $cart = Cart::with(['user', 'product'])->find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
        return response()->json($cart, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/carts/{id}",
     *     summary="Update a cart",
     *     tags={"Carts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="total_price", type="number", format="float"),
     *             @OA\Property(property="product_id", type="integer"),
     *             @OA\Property(property="user_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Cart")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json(['message' => 'Cart not found'], 404);
        }

        $request->validate([
            'quantity' => 'sometimes|integer',
            'total_price' => 'sometimes|numeric',
            'product_id' => 'sometimes|exists:products,product_id',
            'user_id' => 'sometimes|exists:users,user_id',
        ]);        

        $cart->update($request->all());
        return response()->json($cart, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/carts/{id}",
     *     summary="Delete a cart",
     *     tags={"Carts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found"
     *     )
     * )
     */

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
