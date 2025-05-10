<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var User
     */
    protected $user;

    /**
     * OrderRepository constructor.
     *
     * @param Order $order
     * @param Cart $cart
     * @param User $user
     */
    public function __construct(Order $order, Cart $cart, User $user)
    {
        $this->order = $order;
        $this->cart = $cart;
        $this->user = $user;
    }

    /**
     * Get all orders with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator
    {
        $query = $this->order->with(['cart.user', 'cart.product', 'payment']);

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
        return $query->paginate($perPage);
    }

    /**
     * Find an order by ID
     *
     * @param int $id
     * @return Order
     * @throws ModelNotFoundException
     */
    public function findById(int $id): Order
    {
        return $this->order->with(['cart.user', 'cart.product', 'payment'])->findOrFail($id);
    }

    /**
     * Create a new order
     *
     * @param array $data
     * @return Order
     */
    public function create(array $data): Order
    {
        $order = $this->order->create([
            'final_price' => $data['final_price'],
            'cart_id' => $data['cart_id'],
            'payment_id' => $data['payment_id'],
            'order_date' => $data['order_date']
        ]);

        $order->load(['cart.user', 'cart.product', 'payment']);

        return $order;
    }

    /**
     * Update an order
     *
     * @param int $id
     * @param array $data
     * @return Order
     * @throws ModelNotFoundException
     */
    public function update(int $id, array $data): Order
    {
        $order = $this->findById($id);
        $order->update($data);

        $order->load(['cart.user', 'cart.product', 'payment']);

        return $order;
    }

    /**
     * Delete an order
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $order = $this->findById($id);
        return $order->delete();
    }

    /**
     * Get orders by user ID
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        // Find carts belonging to this user
        $cartIds = $this->cart->where('user_id', $userId)->pluck('cart_id');

        // Find orders for these carts
        return $this->order->with(['cart.user', 'cart.product', 'payment'])
                          ->whereIn('cart_id', $cartIds)
                          ->get();
    }

    /**
     * Check if a cart already has an associated order
     *
     * @param int $cartId
     * @param int|null $excludeOrderId
     * @return bool
     */
    public function cartHasOrder(int $cartId, ?int $excludeOrderId = null): bool
    {
        $query = $this->order->where('cart_id', $cartId);

        if ($excludeOrderId !== null) {
            $query->where('order_id', '!=', $excludeOrderId);
        }

        return $query->exists();
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
