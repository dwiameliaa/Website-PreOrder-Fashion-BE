<?php

namespace App\Http\Controllers;

use App\Models\OrderMeasurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrderMeasurementController extends Controller
{

    public function index()
    {
        $orders = OrderMeasurement::with('user')->get();

        if ($orders->isEmpty()) {
            return response()->json([
                "success" => true,
                "message" => "Resource data not found!"
            ], 200);
        }

        // kembalikan dalam format JSON
        return response()->json([
            "success" => true,
            "message" => "Get All Resource",
            "data" => $orders
        ], 200);
    }

    public function store(Request $request)
    {
        // 1. validator dan cek validator
        $validator = Validator::make($request->all(), [
            'product_type' => 'required|in:shirt,pants',
            'measurement_type' => 'required|in:standard,custom',
            'size' => 'nullable|in:XS,S,M,L,XL,XXL,3XL',
            'panjang_bahu' => 'nullable|numeric',
            'panjang_lengan' => 'nullable|numeric',
            'lingkar_dada' => 'nullable|numeric',
            'panjang_baju' => 'nullable|numeric',
            'lingkar_pinggang' => 'nullable|numeric',
            'panjang_celana' => 'nullable|numeric',
            'lingkar_paha' => 'nullable|numeric',
            'lingkar_betis' => 'nullable|numeric',
            'lingkar_lutut' => 'nullable|numeric',
            'lingkar_kaki' => 'nullable|numeric',
            'catatan' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'status' => 'in:requested,in_progress,done,cancelled',
            'total_price' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'data' => $validator->errors(),
            ], 422);
        }

        // 2. generate orderNumber -> unique | ORD-0003
        $uniqueCode = "ORD-" . strtoupper(uniqid());

        // 3. ambil user yang sudah login dan cek login (apakah ada data user?)
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorize!'
            ], 401);
        }

        // upload image
        $image = $request->file('image');
        $image->store('order_measurements', 'public');

        $data = [
            'customer_id' => $user->id,
            'order_number' => $uniqueCode,
            'product_type' => $request->product_type,
            'measurement_type' => $request->measurement_type,
            'status' => 'requested',
            'total_price' => $request->total_price,
            'catatan' => $request->catatan,
            'image' => $image->hashName(),
        ];

        if ($request->measurement_type === 'standard') {
            $data['size'] = $request->size;
        } else {
            $data = array_merge($data, [
                'panjang_bahu' => $request->panjang_bahu,
                'panjang_lengan' => $request->panjang_lengan,
                'lingkar_dada' => $request->lingkar_dada,
                'panjang_baju' => $request->panjang_baju,
                'lingkar_pinggang' => $request->lingkar_pinggang,
                'panjang_celana' => $request->panjang_celana,
                'lingkar_paha' => $request->lingkar_paha,
                'lingkar_betis' => $request->lingkar_betis,
                'lingkar_lutut' => $request->lingkar_lutut,
                'lingkar_kaki' => $request->lingkar_kaki,
            ]);
        }

        $orders = OrderMeasurement::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully!',
            'data' => $orders,
        ], 201);
    }

    public function show(string $id)
    {
        $order = OrderMeasurement::with(['user'])->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Get Detail resource',
            'data' => $order,
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        // 1. Cari data berdasarkan ID
        $order = OrderMeasurement::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }

        // 2. Validasi input (tidak semua required, karena ini update)
        $validator = Validator::make($request->all(), [
            'product_type' => 'nullable|in:shirt,pants',
            'measurement_type' => 'nullable|in:standard,custom',
            'size' => 'nullable|in:XS,S,M,L,XL,XXL,3XL',
            'panjang_bahu' => 'nullable|numeric',
            'panjang_lengan' => 'nullable|numeric',
            'lingkar_dada' => 'nullable|numeric',
            'panjang_baju' => 'nullable|numeric',
            'lingkar_pinggang' => 'nullable|numeric',
            'panjang_celana' => 'nullable|numeric',
            'lingkar_paha' => 'nullable|numeric',
            'lingkar_betis' => 'nullable|numeric',
            'lingkar_lutut' => 'nullable|numeric',
            'lingkar_kaki' => 'nullable|numeric',
            'catatan' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'status' => 'nullable|in:requested,in_progress,done,cancelled',
            'total_price' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'data' => $validator->errors(),
            ], 422);
        }

        // 3. Ambil data yang divalidasi (hanya field yang dikirim user)
        $data = array_filter($validator->validated(), fn($v) => !is_null($v));

        // 4. Handle upload image baru (jika ada)
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->store('order_measurements', 'public');

            // hapus gambar lama (jika ada)
            if ($order->image) {
                Storage::disk('public')->delete('order_measurements/' . $order->image);
            }

            $data['image'] = $image->hashName();
        }

        // 5. Logika pengisian field ukuran
        if (isset($data['measurement_type']) && $data['measurement_type'] === 'standard') {
            // kalau standard → hapus semua field custom
            $data['size'] = $data['size'] ?? $order->size; // tetap pakai size lama jika tidak dikirim
            $data = array_merge($data, [
                'panjang_bahu' => null,
                'panjang_lengan' => null,
                'lingkar_dada' => null,
                'panjang_baju' => null,
                'lingkar_pinggang' => null,
                'panjang_celana' => null,
                'lingkar_paha' => null,
                'lingkar_betis' => null,
                'lingkar_lutut' => null,
                'lingkar_kaki' => null,
            ]);
        } elseif (isset($data['measurement_type']) && $data['measurement_type'] === 'custom') {
            // kalau custom → size harus null
            $data['size'] = null;
        }

        // 6. Update data di database
        $order->update($data);

        // 7. Response
        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully!',
            'data' => $order->fresh(),
        ], 200);
    }


    public function destroy(string $id)
    {
        $order = OrderMeasurement::find($id);

        // Jika data tidak ditemukan
        if (!$order) {
            return response()->json([
                "success" => false,
                "message" => "Order not found"
            ], 404);
        }

        if($order->image) {
            Storage::disk('public')->delete('order_measurements/' . $order->image);
        }

        $order->delete();

        return response()->json([
            "success" => true,
            "message" => "Order deleted successfully"
        ], 200);
    }
}
