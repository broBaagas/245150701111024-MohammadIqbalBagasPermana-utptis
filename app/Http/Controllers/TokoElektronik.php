<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class TokoElektronik extends Controller
{
    private $file = 'Barang.json';

    private function getData()
    {
        if (!Storage::disk('local')->exists($this->file)) {
            Storage::disk('local')->put($this->file, json_encode([]));
        }

        return json_decode(Storage::disk('local')->get($this->file), true);
    }

    private function saveData($data)
    {
        Storage::disk('local')->put($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function findItem($Barang, $id)
    {
        foreach ($Barang as $index => $item) {
            if ($item['id'] == $id) {
                return [$item, $index];
            }
        }
        return [null, null];
    }

    #[OA\Get(
        path: "/api/items",
        summary: "Ambil semua barang",
        tags: ["Items"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Berhasil mengambil data"
            )
        ]
    )]

    #[OA\Get(
        path: "/api/items/{id}",
        summary: "Ambil barang berdasarkan ID",
        tags: ["Items"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Data ditemukan"),
            new OA\Response(response: 404, description: "Item tidak ditemukan")
        ]
    )]

    #[OA\Post(
        path: "/api/items",
        summary: "Tambah barang",
        tags: ["Items"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["kode_barang", "name", "price"],
                properties: [
                    new OA\Property(property: "kode_barang", type: "string", example: "PH001"),
                    new OA\Property(property: "name", type: "string", example: "Lampu Philips"),
                    new OA\Property(property: "price", type: "number", example: 50000)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Berhasil ditambahkan"),
            new OA\Response(response: 422, description: "Validasi gagal")
        ]
    )]

    #[OA\Put(
        path: "/api/items/{id}",
        summary: "Update seluruh data barang",
        tags: ["Items"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["kode_barang", "name", "price"],
                properties: [
                    new OA\Property(property: "kode_barang", type: "string"),
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "price", type: "number")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Berhasil update"),
            new OA\Response(response: 404, description: "Item tidak ditemukan"),
            new OA\Response(response: 422, description: "Validasi gagal")
        ]
    )]

    #[OA\Patch(
        path: "/api/items/{id}",
        summary: "Update sebagian data barang",
        tags: ["Items"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "kode_barang", type: "string"),
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "price", type: "number")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Berhasil patch"),
            new OA\Response(response: 404, description: "Item tidak ditemukan"),
            new OA\Response(response: 422, description: "Validasi gagal")
        ]
    )]

    #[OA\Delete(
        path: "/api/items/{id}",
        summary: "Hapus barang",
        tags: ["Items"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Berhasil dihapus"),
            new OA\Response(response: 404, description: "Item tidak ditemukan")
        ]
    )]

    // GET api/items
    public function index()
    {
        return response()->json($this->getData());
    }

    // GET api/items/{id}
    public function show($id)
    {
        $Barang = $this->getData();
        [$item, $index] = $this->findItem($Barang, $id);

        if (!$item) {
            return response()->json([
                'message' => "Item dengan ID $id tidak ditemukan"
            ], 404);
        }

        return response()->json($item);
    }

    // POST api/items
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_barang' => ['required', 'string', 'regex:/^[A-Za-z]{2}[0-9]{3}$/'],
            'name' => 'required|string|min:3|max:100',
            'price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $Barang = $this->getData();

        foreach ($Barang as $item) {
            if (strtoupper($item['kode_barang']) === strtoupper($validated['kode_barang'])) {
                return response()->json([
                    'message' => 'Kode barang sudah digunakan'
                ], 422);
            }
        }

        $newItem = [
            'id' => count($Barang) ? max(array_column($Barang, 'id')) + 1 : 1,
            'kode_barang' => strtoupper($validated['kode_barang']),
            'name' => $validated['name'],
            'price' => $validated['price']
        ];

        $Barang[] = $newItem;
        $this->saveData($Barang);

        return response()->json($newItem, 201);
    }

    // PUT api/items/{id}
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kode_barang' => ['required', 'string', 'regex:/^[A-Za-z]{2}[0-9]{3}$/'],
            'name' => 'required|string|min:3|max:100',
            'price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $Barang = $this->getData();
        [$item, $index] = $this->findItem($Barang, $id);

        if (!$item) {
            return response()->json([
                'message' => "Item tidak ditemukan"
            ], 404);
        }

        foreach ($Barang as $i => $it) {
            if ($it['kode_barang'] === $validated['kode_barang'] && $i != $index) {
                return response()->json([
                    'message' => 'Kode barang sudah digunakan'
                ], 422);
            }
        }

        $Barang[$index] = [
            'id' => $id,
            'kode_barang' => strtoupper($validated['kode_barang']),
            'name' => $validated['name'],
            'price' => $validated['price']
        ];

        $this->saveData($Barang);

        return response()->json($Barang[$index]);
    }

    // PATCH api/items/{id}
    public function patch(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kode_barang' => ['sometimes', 'string', 'regex:/^[A-Za-z]{2}[0-9]{3}$/'],
            'name' => 'sometimes|string|min:3|max:100',
            'price' => 'sometimes|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $Barang = $this->getData();
        [$item, $index] = $this->findItem($Barang, $id);

        if (!$item) {
            return response()->json([
                'message' => "Item tidak ditemukan"
            ], 404);
        }

        if (isset($validated['kode_barang'])) {
            foreach ($Barang as $i => $it) {
                if ($it['kode_barang'] === $validated['kode_barang'] && $i != $index) {
                    return response()->json([
                        'message' => 'Kode barang sudah digunakan'
                    ], 422);
                }
            }

            $Barang[$index]['kode_barang'] = strtoupper($validated['kode_barang']);
        }

        if (isset($validated['name'])) {
            $Barang[$index]['name'] = $validated['name'];
        }

        if (isset($validated['price'])) {
            $Barang[$index]['price'] = $validated['price'];
        }

        $this->saveData($Barang);

        return response()->json($Barang[$index]);
    }

    // DELETE api/items/{id}
    public function destroy($id)
    {
        $Barang = $this->getData();
        [$item, $index] = $this->findItem($Barang, $id);

        if (!$item) {
            return response()->json([
                'message' => "Item tidak ditemukan"
            ], 404);
        }

        array_splice($Barang, $index, 1);
        $this->saveData($Barang);

        return response()->json([
            'message' => 'Item berhasil dihapus'
        ]);
    }
}
