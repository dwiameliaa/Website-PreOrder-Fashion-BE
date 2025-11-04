<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user', 'product')->get();

        if ($orders->isEmpty()) {
            return response()->json([
                "success" => true,
                "message" => "Resource data not found!"
            ], 200);
        }

        return response()->json([
            "success" => true,
            "message" => "Get All Resource",
            "data" => $orders
        ], 200);
    }

    public function store(Request $request)
    {
        // 1. Validator
        $validator = Validator::make($request->all(), [
            'size' => 'required|in:XS,S,M,L,XL,XXL,3XL',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // 2. Cek validator error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $uniqueCode = "ORD-" . strtoupper(uniqid());

        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorize!'
            ], 401);
        }

        $product = Product::find($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock!'
            ], 400);
        }

        // 3. hitung total harga = price * quantity
        $totalAmount = $product->price * $request->quantity;

        // kurangi stok product(update)
        $product->stock -= $request->quantity;
        $product->save();

        // 4. Simpan data
        $product = Order::create([
            'order_number' => $uniqueCode,
            'size' => $request->size,
            'total_price' => $totalAmount,
            'customer_id' => $user->id,
            'product_id' => $request->product_id,
        ]);

        // 5. Response
        return response()->json([
            'success' => true,
            'message' => 'Order added successfully!',
            'data' => $product,
        ], 201);
    }

    public function show(string $id)
    {
        $order = Order::with(['user', 'product'])->find($id);

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

    public function update(string $id, Request $request)
    {
        // 1. Mencari data
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                "success" => false,
                "message" => "Order not found"
            ], 404);
        }

        // 2. Validator
        $validator = Validator::make($request->all(), [
            'size' => 'required|in:XS,S,M,L,XL,XXL,3XL',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // 3. Kembalikan stok lama
        $oldProduct = Product::find($order->product_id);
        $oldProduct->stock += $order->quantity;
        $oldProduct->save();

        // 4. Ambil product baru
        $product = Product::find($request->product_id);

        // 5. Cek stok product baru
        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock!'
            ], 400);
        }

        // 6. Kurangi stok product baru
        $product->stock -= $request->quantity;
        $product->save();

        // 7. Hitung total harga
        $totalAmount = $product->price * $request->quantity;

        // 8. Update data transaksi
        $data = [
            'size' => $request->size,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total_price' => $totalAmount,
        ];

        $order->update($data);

        // 9. Kembalikan response
        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully!',
            'data' => $order->fresh()
        ], 200);
    }

    public function destroy(string $id)
    {
        $order = Order::find($id);

        // Jika data tidak ditemukan
        if (!$order) {
            return response()->json([
                "success" => false,
                "message" => "Order not found"
            ], 404);
        }

        // Jika berhasil dihapus
        $order->delete();

        return response()->json([
            "success" => true,
            "message" => "Order deleted successfully"
        ], 200);
    }
}
