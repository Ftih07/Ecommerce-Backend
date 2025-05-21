<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Repositories\Interfaces\CartRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * @var CartRepository
     */
    protected $cartRepository;

    /**
     * CartController constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }
    /**
     * @OA\Get(
     *     path="/carts",
     *     security={{"bearerAuth":{}}},
     *     summary="Get all carts",
     *     description="Retrieves all shopping carts with optional filtering and pagination",
     *     tags={"Carts"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *         description="Filter by user ID",
     *         @OA\Schema(type="integer")
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
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Cart")),
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
        $carts = $this->cartRepository->getAll($request);

        return response()->json([
            'data' => $carts->items(),
            'meta' => [
                'current_page' => $carts->currentPage(),
                'last_page' => $carts->lastPage(),
                'per_page' => $carts->perPage(),
                'total' => $carts->total()
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/carts",
     *     security={{"bearerAuth":{}}},
     *     summary="Create a new cart",
     *     description="Create a new shopping cart item with product price calculation",
     *     tags={"Carts"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"quantity", "product_id", "user_id"},
     *             @OA\Property(property="quantity", type="integer", example=2),
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cart created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Cart")
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
     *         response=404,
     *         description="Product not found"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'product_id' => 'required|exists:products,product_id',
            'user_id' => 'required|exists:users,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cart = $this->cartRepository->create($request->all());
            return response()->json($cart, 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/carts/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Get a cart by ID",
     *     description="Retrieve detailed shopping cart information by ID",
     *     tags={"Carts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Cart ID",
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
        try {
            $cart = $this->cartRepository->findById($id);
            return response()->json($cart, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/carts/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Update a cart",
     *     description="Update cart details with automatic price recalculation",
     *     tags={"Carts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Cart ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="quantity", type="integer", example=3),
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1)
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
            $validator = Validator::make($request->all(), [
                'quantity' => 'sometimes|integer|min:1',
                'product_id' => 'sometimes|exists:products,product_id',
                'user_id' => 'sometimes|exists:users,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cart = $this->cartRepository->update($id, $request->all());
            return response()->json($cart, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/carts/{id}",
     *     security={{"bearerAuth":{}}},
     *     summary="Delete a cart",
     *     description="Delete a shopping cart item",
     *     tags={"Carts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Cart ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cart deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cart deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cart not found"
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Cannot delete cart with existing orders",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cannot delete cart with existing orders")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            if ($this->cartRepository->hasOrders($id)) {
                return response()->json([
                    'message' => 'Cannot delete cart with existing orders'
                ], 409);
            }

            $this->cartRepository->delete($id);
            return response()->json(['message' => 'Cart deleted'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Cart not found'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/users/{user_id}/carts",
     *     security={{"bearerAuth":{}}},
     *     summary="Get user's cart items",
     *     description="Retrieve all cart items for a specific user",
     *     tags={"Carts"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User's cart items retrieved successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Cart")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     )
     * )
     */
    public function getUserCarts($userId)
    {
        if (!$this->cartRepository->userExists($userId)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $carts = $this->cartRepository->getByUserId($userId);
        return response()->json($carts, 200);
    }
}
