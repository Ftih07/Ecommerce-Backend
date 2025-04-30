<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * @OA\Get(
     *     path="/stores",
     *     summary="Get all stores",
     *     description="Mengambil semua data toko",
     *     operationId="getStores",
     *     tags={"Stores"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil data toko",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Store"))
     *     )
     * )
     */
    // GET /stores
    public function index()
    {
        return response()->json(Store::all(), 200);
    }

    /**
     * @OA\Post(
     *     path="/stores",
     *     summary="Create a new store",
     *     description="Membuat toko baru",
     *     operationId="createStore",
     *     tags={"Stores"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "city"},
     *             @OA\Property(property="name", type="string", example="Toko Elektronik"),
     *             @OA\Property(property="city", type="string", example="Bandung"),
     *             @OA\Property(property="profile_image", type="string", nullable=true, example="http://example.com/image.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Store berhasil dibuat",
     *         @OA\JsonContent(ref="#/components/schemas/Store")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Request tidak valid"
     *     )
     * )
     */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'city' => 'required|string',
            'profile_image' => 'nullable|string',
        ]);

        $store = Store::create($request->all());
        return response()->json($store, 201);
    }

    /**
     * @OA\Get(
     *     path="/stores/{id}",
     *     summary="Get a store by ID",
     *     description="Mengambil data toko berdasarkan ID",
     *     operationId="getStoreById",
     *     tags={"Stores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID toko",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Toko ditemukan",
     *         @OA\JsonContent(ref="#/components/schemas/Store")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Toko tidak ditemukan"
     *     )
     * )
     */
    // GET /stores/{id}

    public function show($id)
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }
        return response()->json($store, 200);
    }

    /**
     * @OA\Put(
     *     path="/stores/{id}",
     *     summary="Update store data",
     *     description="Memperbarui data toko berdasarkan ID",
     *     operationId="updateStore",
     *     tags={"Stores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID toko",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Toko Gadget"),
     *             @OA\Property(property="city", type="string", example="Jakarta"),
     *             @OA\Property(property="profile_image", type="string", nullable=true, example="http://example.com/gadget.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Toko berhasil diperbarui",
     *         @OA\JsonContent(ref="#/components/schemas/Store")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Toko tidak ditemukan"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }

        $store->update($request->all());
        return response()->json($store, 200);
    }

    /**
     * @OA\Delete(
     *     path="/stores/{id}",
     *     summary="Delete a store",
     *     description="Menghapus toko berdasarkan ID",
     *     operationId="deleteStore",
     *     tags={"Stores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID toko",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Toko berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Store deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Toko tidak ditemukan"
     *     )
     * )
     */

    public function destroy($id)
    {
        $store = Store::find($id);
        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }
        $store->delete();
        return response()->json(['message' => 'Store deleted'], 200);
    }
}
