<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentRepository implements PaymentRepositoryInterface
{
    /**
     * @var Payment
     */
    protected $payment;

    /**
     * PaymentRepository constructor.
     *
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get all payments
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->payment->all();
    }

    /**
     * Find a payment by ID
     *
     * @param int $id
     * @return Payment|null
     */
    public function findById(int $id): ?Payment
    {
        return $this->payment->find($id);
    }

    /**
     * Find a payment by ID or fail
     *
     * @param int $id
     * @return Payment
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $id): Payment
    {
        return $this->payment->findOrFail($id);
    }

    /**
     * Create a new payment
     *
     * @param array $data
     * @return Payment
     */
    public function create(array $data): Payment
    {
        return $this->payment->create($data);
    }

    /**
     * Update a payment
     *
     * @param int $id
     * @param array $data
     * @return Payment
     */
    public function update(int $id, array $data): Payment
    {
        $payment = $this->findById($id);

        if ($payment) {
            $payment->update($data);
        }

        return $payment;
    }

    /**
     * Delete a payment
     *
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $payment = $this->findOrFail($id);
        return $payment->delete();
    }

    /**
     * Check if the payment has associated orders
     *
     * @param int $paymentId
     * @return bool
     */
    public function hasOrders(int $paymentId): bool
    {
        $payment = $this->findById($paymentId);
        if (!$payment) {
            return false;
        }

        return $payment->order()->exists();
    }
}
