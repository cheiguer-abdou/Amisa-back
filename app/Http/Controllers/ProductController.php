<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page', 1); // Default to page 1 if not provided
        $size = $request->input('size', 5); // Default to 5 rows per page if not provided

        $products = Product::orderBy('created_at', 'desc')->paginate($size, ['*'], 'page', $page);

        // Add image URLs to each product
        $products->getCollection()->transform(function ($product) {
            if ($product->image) {
                // Assuming image path is already correct, no need to modify it
                $product->image_url = asset("storage/products_images/{$product->image}");
            } else {
                $product->image_url = null; // Set image URL to null if no image is available
            }
            return $product;
        });

        return response()->json($products, 200);
    }

    public function productsPrice()
    {
        $products = Product::all();
        $profit = 0;
        $sales = 0;

        foreach ($products as $product) {
            $profit += $product->price * $product->quantity - $product->initPrice * $product->quantity;
            $sales += $product->initPrice * $product->quantity;
        }
        return response()->json(["profit" => $profit, "sales" => $sales], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'initPrice' => 'required|numeric',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $productData = $request->all();
        $imageName = "";

        if ($request->hasFile('image')) {
            $imageName = $request->name . '.' . $request->file('image')->getClientOriginalExtension();
            $imagePath = $request->file('image')->storeAs('products_images', $imageName, 'public');
            $productData['image'] = $imageName;
        }

        $product = Product::create($productData);

        // Update budget
        $budget = Budget::firstOrFail();
        $totalCost = $product->initPrice * $product->quantity;
        $budget->budget -= $totalCost;
        $budget->save();

        return response()->json(['message' => 'Product created successfully', 'data' => $imageName], 201);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product, 200);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        } // Log the received request data
        Log::info('Received request data:', $request->all());

        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'initPrice' => 'required|numeric',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $productData = $request->all();
        Log::info('Received request data:', $request->all());

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $imagePath = $request->file('image')->store('products_images', 'public'); // stocke la nouvelle image
            $productData['image'] = $imagePath;
        }

        $product->update($productData);

        return response()->json(['message' => 'Product updated successfully'], 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    public function indexByClient($clientId)
    {
        $client = Client::findOrFail($clientId);
        $products = $client->products;
        return response()->json(['data' => $products], 200);
    }

    public function searchProducts(Request $request)
    {
        $keyword = $request->input('keyword');

        $products = Product::where('name', 'like', "%$keyword%")
            ->orWhere('description', 'like', "%$keyword%")
            ->paginate(5);

        return response()->json($products, 200);
    }
}
