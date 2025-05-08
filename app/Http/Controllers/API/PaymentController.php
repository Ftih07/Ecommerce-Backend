<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/payments",
     *     summary="Get all payments",
     *     description="Retrieve a list of all payment records",
     *     operationId="getPayments",
     *     tags={"Payments"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Payment"))
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Payment::all(), 200);
    }

    /**
     * @OA\Post(
     *     path="/payments",
     *     summary="Create a new payment",
     *     description="Create a new payment record",
     *     operationId="createPayment",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payment_method", "status"},
     *             @OA\Property(property="payment_method", type="string", example="Credit Card"),
     *             @OA\Property(property="status", type="string", enum={"pending", "paid", "failed"}, example="pending")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
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
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'status' => 'required|in:pending,paid,failed',
        ]);

        $payment = Payment::create($request->all());
        return response()->json($payment, 201);
    }

    /**
     * @OA\Get(
     *     path="/payments/{id}",
     *     summary="Get a payment by ID",
     *     description="Retrieve specific payment details by ID",
     *     operationId="getPaymentById",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Payment ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment found",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }
        return response()->json($payment, 200);
    }

    /**
     * @OA\Put(
     *     path="/payments/{id}",
     *     summary="Update a payment",
     *     description="Update an existing payment record",
     *     operationId="updatePayment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Payment ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="payment_method", type="string", example="PayPal"),
     *             @OA\Property(property="status", type="string", enum={"pending", "paid", "failed"}, example="paid")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found")
     *         )
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
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $request->validate([
            'payment_method' => 'sometimes|string',
            'status' => 'sometimes|in:pending,paid,failed',
        ]);

        $payment->update($request->all());
        return response()->json($payment, 200);
    }

    /**
     * @OA\Delete(
     *     path="/payments/{id}",
     *     summary="Delete a payment",
     *     description="Delete an existing payment record",
     *     operationId="deletePayment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Payment ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Cannot delete payment with associated orders",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cannot delete payment with associated orders")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $payment = Payment::with('order')->findOrFail($id);

            if ($payment->order) {
                return response()->json([
                    'message' => 'Cannot delete payment with associated orders'
                ], 409);
            }

            $payment->delete();
            return response()->json(['message' => 'Payment deleted'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Payment not found'], 404);
        }
    }
}
