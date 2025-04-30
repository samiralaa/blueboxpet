<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        return response()->json(Stock::all(), 200);
    }

    // Get a single product
    public function show($id)
    {
        $product = Stock::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product, 200);
    }

    // Create a new product
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required',
            'name_ar' => 'required',
            'description' => 'nullable',
            'category_id' => 'nullable',
            'quantity' => 'required|string',
            'weight' => 'required|string',
            'type' => 'required|string',
            'sku' => 'required|string',
            'expiration_data' => 'required|date',
            'pricea' => 'nullable',
            'priceb' => 'nullable',
            'pricec' => 'nullable',
            'affiliationprice' => 'nullable',
            'price' => 'nullable',
        ]);

        $product = Stock::create($validated);
  $product->sku = $product->sku ."_". $product->id;
    $product->save();
        return response()->json($product, 201);
    }

    // Update a product
   public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name_en' => 'required',
        'name_ar' => 'required',
        'description' => 'nullable',
        'category_id' => 'nullable',
        'quantity' => 'required|string',
        'weight' => 'required|string',
        'type' => 'required|string',
        'expiration_data' => 'required|date',
        'pricea' => 'nullable',
        'priceb' => 'nullable',
        'sku' => 'nullable|string',
        'pricec' => 'nullable',
        'affiliationprice' => 'nullable',
        'price' => 'nullable',
    ]);

    $product = Stock::findOrFail($id);
    $product->update($validated);

    
   
    $product->save();

    return response()->json($product);
}


    // Delete a product
    public function delete($id)
    {
        $product = Stock::find($id);

        if (!$product) {
            return response()->json(['message' => 'Stock not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Stock deleted successfully'], 200);
    }
    
    public function updateQuantity($id, Request $request)
{
    $request->validate([
        'quantity' => 'required|integer|min:0', // Ensure quantity is a non-negative integer
    ]);

    $stock = Stock::findOrFail($id); // Use findOrFail to handle cases where the stock doesn't exist
    $stock->update(['quantity' => $request->quantity]);

    return response()->json(['message' => 'Stock updated successfully'], 200);
}
}
