<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Cart;

interface CartRepositoryInterface
{
    /**
     * Get all carts with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator;

    /**
     * Find a cart by ID
     *
     * @param int $id
     * @return Cart
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): Cart;

    /**
     * Create a new cart
     *
     * @param array $data
     * @return Cart
     */
    public function create(array $data): Cart;

    /**
     * Update a cart
     *
     * @param int $id
     * @param array $data
     * @return Cart
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Cart;

    /**
     * Delete a cart
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool;

    /**
     * Get carts by user ID
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByUserId(int $userId): \Illuminate\Database\Eloquent\Collection;

    /**
     * Calculate the total price for a cart item
     *
     * @param int $productId
     * @param int $quantity
     * @return float
     */
    public function calculateTotalPrice(int $productId, int $quantity): float;

    /**
     * Check if a cart has associated orders
     *
     * @param int $cartId
     * @return bool
     */
    public function hasOrders(int $cartId): bool;
}