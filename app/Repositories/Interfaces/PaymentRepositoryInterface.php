<?php

namespace App\Repositories\Interfaces;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryInterface
{
    /**
     * Get all payments
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Find a payment by ID
     *
     * @param int $id
     * @return Payment|null
     */
    public function findById(int $id): ?Payment;

    /**
     * Find a payment by ID or fail
     *
     * @param int $id
     * @return Payment
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Payment;

    /**
     * Create a new payment
     *
     * @param array $data
     * @return Payment
     */
    public function create(array $data): Payment;

    /**
     * Update a payment
     *
     * @param int $id
     * @param array $data
     * @return Payment
     */
    public function update(int $id, array $data): Payment;

    /**
     * Delete a payment
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool;

    /**
     * Check if a payment has associated orders
     *
     * @param int $paymentId
     * @return bool
     */
    public function hasOrders(int $paymentId): bool;
}
