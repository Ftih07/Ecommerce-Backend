<?php

namespace App\Http\Controllers\API;

use App\Models\Store;
use App\Repositories\Interfaces\StoreRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * StoreController constructor.
     *
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(StoreRepositoryInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }
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
    public function index(Request $request)
    {
        $stores = $request->has(['per_page', 'page', 'sort_by', 'sort_order', 'name', 'city'])
            ? $this->storeRepository->getAll($request)
            : $this->storeRepository->getAllStores();

        return response()->json($stores, 200);
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

        $store = $this->storeRepository->create($request->all());
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
        $store = $this->storeRepository->findById($id);
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
        $request->validate([
            'name' => 'sometimes|string',
            'city' => 'sometimes|string',
            'profile_image' => 'nullable|string',
        ]);

        $store = $this->storeRepository->update($id, $request->all());

        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }

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
        $result = $this->storeRepository->delete($id);

        if (!$result) {
            return response()->json(['message' => 'Store not found'], 404);
        }

        return response()->json(['message' => 'Store deleted'], 200);
    }

    /**
     * @OA\Get(
     *     path="/stores/{id}/products",
     *     summary="Get store with its products",
     *     description="Mengambil data toko beserta produk-produknya",
     *     operationId="getStoreWithProducts",
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
     *         description="Berhasil mengambil data toko beserta produknya",
     *         @OA\JsonContent(ref="#/components/schemas/Store")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Toko tidak ditemukan"
     *     )
     * )
     */
    public function getStoreWithProducts($id)
    {
        $store = $this->storeRepository->findByIdWithProducts($id);

        if (!$store) {
            return response()->json(['message' => 'Store not found'], 404);
        }

        return response()->json($store, 200);
    }

    /**
     * @OA\Get(
     *     path="/stores/search",
     *     summary="Search stores by name",
     *     description="Mencari toko berdasarkan nama",
     *     operationId="searchStoresByName",
     *     tags={"Stores"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=true,
     *         description="Nama toko yang ingin dicari",
     *         @OA\Schema(type="string", example="Elektronik")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Jumlah data per halaman",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Halaman yang ingin ditampilkan",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mencari toko",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function search(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $stores = $this->storeRepository->searchByName($request->name, $request);
        return response()->json($stores, 200);
    }

    /**
     * @OA\Get(
     *     path="/stores/city/{city}",
     *     summary="Get stores by city",
     *     description="Mengambil data toko berdasarkan kota",
     *     operationId="getStoresByCity",
     *     tags={"Stores"},
     *     @OA\Parameter(
     *         name="city",
     *         in="path",
     *         required=true,
     *         description="Nama kota",
     *         @OA\Schema(type="string", example="Bandung")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Jumlah data per halaman",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Halaman yang ingin ditampilkan",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mengambil data toko berdasarkan kota",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function getByCity($city, Request $request)
    {
        $stores = $this->storeRepository->getByCity($city, $request);
        return response()->json($stores, 200);
    }
}
