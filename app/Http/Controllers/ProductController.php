<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function find($id)
    {
        // Find product by ID
        $product = Product::with(['category:id,name,image'])->findOrFail($id);

        return response()->json([
            'message' => 'Product fetched successfully.',
            'product' => new ProductResource($product)
        ]);
    }
    public function index(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:480'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $perPage = $request->input('per_page', 15);

        $query = Product::query()->with(['category:id,name']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('category', fn($r) => $r->where('name', 'like', "%$search%"))
                    ->orWhere('title', 'like', "%$search%")
                    ->orWhere('desc', 'like', "%$search%");
            });
        }

        $products = $query->paginate($perPage, ['id', 'title', 'desc', 'price', 'stock', 'thumbnail', 'category_id']);

        return response()->json([
            'message' => 'Fetched all products',
            'data' => ProductResource::collection($products),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'short_desc' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|max:1024',
            'category_id' => 'required|integer|exists:categories,id',
            'expired_at' => 'required|date|after:now',
        ]);

        // Handle thumbnail upload
        $thumbnail = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('products', 'public')
            : 'products/no_photo.jpg';

        // Create product
        $product = Product::create([
            'title' => $request->title,
            'desc' => $request->desc,
            'short_desc' => $request->short_desc,
            'price' => $request->price,
            'stock' => $request->stock,
            'thumbnail' => $thumbnail,
            'category_id' => $request->category_id,
            'status' => 2,
            'published_at' => now(),
            'expired_at' => $request->expired_at,
        ]);

        return response()->json([
            'message' => 'Product saved successfully.',
            'product' => new ProductResource($product)
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'short_desc' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|max:1024',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'expired_at' => 'nullable|date|after:now',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        // Fetch product
        $product = Product::findOrFail($id);

        // Handle thumbnail replacement
        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }
            $product->thumbnail = $request->file('thumbnail')->store('products', 'public');
        }

        // Update product data
        $product->update($request->only(['title', 'desc', 'short_desc', 'category_id', 'price', 'stock', 'expired_at']));

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => new ProductResource($product)
        ]);
    }

    public function destroy($id)
    {
        // Find product
        $product = Product::findOrFail($id);

        // Delete thumbnail if exists
        if ($product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
        }

        // Delete product
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ]);
    }
}
