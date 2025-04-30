<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/payments",
     *     summary="Get all payments",
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
     *     path="/api/payments",
     *     summary="Create a new payment",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"payment_method", "status"},
     *             @OA\Property(property="payment_method", type="string"),
     *             @OA\Property(property="status", type="string", enum={"pending", "paid", "failed"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
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
            'payment_method' => 'required|string',
            'status' => 'required|in:pending,paid,failed',
        ]);

        $payment = Payment::create($request->all());
        return response()->json($payment, 201);
    }
    /**
     * @OA\Get(
     *     path="/api/payments/{id}",
     *     summary="Get a payment by ID",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment found",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
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
     *     path="/api/payments/{id}",
     *     summary="Update a payment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="payment_method", type="string"),
     *             @OA\Property(property="status", type="string", enum={"pending", "paid", "failed"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Payment")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
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
     *     path="/api/payments/{id}",
     *     summary="Delete a payment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found"
     *     )
     * )
     */
    public function destroy($id)
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }
        $payment->delete();
        return response()->json(['message' => 'Payment deleted'], 200);
    }
}

/**
 * @OA\Schema(
 *     schema="Payment",
 *     type="object",
 *     title="Payment",
 *     required={"id", "payment_method", "status"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="payment_method", type="string", example="Credit Card"),
 *     @OA\Property(property="status", type="string", example="paid"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
