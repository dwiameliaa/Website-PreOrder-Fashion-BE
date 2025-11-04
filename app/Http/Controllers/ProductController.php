<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            return response()->json([
                "success" => true,
                "message" => "Resource data not found!"
            ], 200);
        }

        return response()->json([
            "success" => true,
            "message" => "Get All Resource",
            "data" => $products
        ], 200);
    }

    public function store(Request $request)
    {
        // 1. Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // 2. Cek validator error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // 3. Upload image
        $image = $request->file('image');
        $image->store('products', 'public');

        // 4. Simpan data
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $image->hashName(),
        ]);

        // 5. Response
        return response()->json([
            'success' => true,
            'message' => 'Product added successfully!',
            'data' => $product,
        ], 201);
    }

    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Get Detail resource',
            'data' => $product,
        ], 200);
    }

    public function update(string $id, Request $request)
    {
        // 1. mencari data
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                "success" => false,
                "message" => "Product not found"
            ], 404);
        }

        // 2. validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // 3. siapkan data yang ingin di update
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
        ];

        // 4. handle image (upload image dan delete image)
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->store('products', 'public');

            if ($product->image) {
                Storage::disk('public')->delete('products/' . $product->image);
            }

            $data['image'] = $image->hashName();
        }

        // 5. update data baru ke database
        $product->update($data);

        return response()->json([
            'succes' => true,
            'massage' => 'Resource updated successfully!',
            'data' => $product,
        ], 200);
    }

    public function destroy(string $id)
    {
        $product = Product::find($id);

        // Jika data tidak ditemukan
        if (!$product) {
            return response()->json([
                "success" => false,
                "message" => "Product not found"
            ], 404);
        }

        if($product->image) {
            // delete from storage
            Storage::disk('public')->delete('products/' . $product->image);
        }

        // Jika berhasil dihapus
        $product->delete();

        return response()->json([
            "success" => true,
            "message" => "Product deleted successfully"
        ], 200);
    }
}
