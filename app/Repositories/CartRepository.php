<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Interfaces\CartRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CartRepository implements CartRepositoryInterface
{
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var User
     */
    protected $user;

    /**
     * CartRepository constructor.
     *
     * @param Cart $cart
     * @param Product $product
     * @param User $user
     */
    public function __construct(Cart $cart, Product $product, User $user)
    {
        $this->cart = $cart;
        $this->product = $product;
        $this->user = $user;
    }

    /**
     * Get all carts with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator
    {
        $query = $this->cart->with(['user', 'product', 'order']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $perPage = $request->per_page ?? 15;
        return $query->paginate($perPage);
    }

    /**
     * Find a cart by ID
     *
     * @param int $id
     * @return Cart
     * @throws ModelNotFoundException
     */
    public function findById(int $id): Cart
    {
        return $this->cart->with(['user', 'product', 'order'])->findOrFail($id);
    }

    /**
     * Create a new cart
     *
     * @param array $data
     * @return Cart
     */
    public function create(array $data): Cart
    {
        $totalPrice = $this->calculateTotalPrice($data['product_id'], $data['quantity']);

        $cartData = [
            'quantity' => $data['quantity'],
            'total_price' => $totalPrice,
            'product_id' => $data['product_id'],
            'user_id' => $data['user_id'],
        ];

        $cart = $this->cart->create($cartData);
        $cart->load(['user', 'product']);

        return $cart;
    }

    /**
     * Update a cart
     *
     * @param int $id
     * @param array $data
     * @return Cart
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): Cart
    {
        $cart = $this->findById($id);

        $productId = $data['product_id'] ?? $cart->product_id;
        $quantity = $data['quantity'] ?? $cart->quantity;

        if (isset($data['quantity']) || isset($data['product_id'])) {
            $totalPrice = $this->calculateTotalPrice($productId, $quantity);
            $cart->total_price = $totalPrice;
        }

        if (isset($data['quantity'])) $cart->quantity = $quantity;
        if (isset($data['product_id'])) $cart->product_id = $productId;
        if (isset($data['user_id'])) $cart->user_id = $data['user_id'];

        $cart->save();
        $cart->load(['user', 'product']);

        return $cart;
    }

    /**
     * Delete a cart
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $cart = $this->findById($id);

        if ($this->hasOrders($id)) {
            return false;
        }

        return $cart->delete();
    }

    /**
     * Get carts by user ID
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        return $this->cart->with(['product'])
                    ->where('user_id', $userId)
                    ->get();
    }

    /**
     * Calculate the total price for a cart item
     *
     * @param int $productId
     * @param int $quantity
     * @return float
     * @throws ModelNotFoundException
     */
    public function calculateTotalPrice(int $productId, int $quantity): float
    {
        $product = $this->product->findOrFail($productId);
        return $product->price * $quantity;
    }

    /**
     * Check if a cart has associated orders
     *
     * @param int $cartId
     * @return bool
     */
    public function hasOrders(int $cartId): bool
    {
        $cart = $this->cart->with('order')->findOrFail($cartId);
        return (bool) $cart->order;
    }

    /**
     * Check if user exists
     *
     * @param int $userId
     * @return bool
     */
    public function userExists(int $userId): bool
    {
        return $this->user->where('user_id', $userId)->exists();
    }
}
