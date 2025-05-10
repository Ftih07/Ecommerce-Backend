<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    /**
     * Get all orders with optional filtering and pagination
     *
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getAll(Request $request): LengthAwarePaginator;

    /**
     * Find an order by ID
     *
     * @param int $id
     * @return Order
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): Order;

    /**
     * Create a new order
     *
     * @param array $data
     * @return Order
     */
    public function create(array $data): Order;

    /**
     * Update an order
     *
     * @param int $id
     * @param array $data
     * @return Order
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): Order;

    /**
     * Delete an order
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool;

    /**
     * Get orders by user ID
     *
     * @param int $userId
     * @return Collection
     */
    public function getByUserId(int $userId): Collection;

    /**
     * Check if a cart already has an associated order
     *
     * @param int $cartId
     * @param int|null $excludeOrderId
     * @return bool
     */
    public function cartHasOrder(int $cartId, ?int $excludeOrderId = null): bool;

    /**
     * Check if user exists
     *
     * @param int $userId
     * @return bool
     */
    public function userExists(int $userId): bool;
}
