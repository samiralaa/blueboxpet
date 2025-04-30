<?php

namespace App\Http\Controllers;  // Make sure this is at the top

use App\Models\SubDilivery;
use Illuminate\Http\Request;

class SubDiliveryController extends Controller  // Make sure the class name is properly declared
{
  public function index()
{
    $subDiliveries = SubDilivery::all();

    // Loop through each subDilivery and append the full URL for the image
    $subDiliveries->each(function ($subDilivery) {
        if ($subDilivery->image) {
            // Generate the full URL for the image
            $subDilivery->image_url = asset('public/storage/' . $subDilivery->image);
        }
    });

    return response()->json($subDiliveries);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048', // validate as file
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'title' => 'required|string|max:255',
            'text' => 'nullable|string',
        ]);

        // Handle file upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/sub_diliveries', 'public');
        }

        $subDilivery = SubDilivery::create($data);

        return response()->json(['message' => 'Created', 'data' => $subDilivery], 201);
    }

    public function show($id)
    {
        return SubDilivery::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $subDilivery = SubDilivery::findOrFail($id);

        $data = $request->validate([
            'image' => 'nullable|string',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'title' => 'required|string|max:255',
            'text' => 'nullable|string',
        ]);

        $subDilivery->update($data);

        return response()->json(['message' => 'Updated', 'data' => $subDilivery]);
    }

    public function destroy($id)
    {
        $subDilivery = SubDilivery::findOrFail($id);
        $subDilivery->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
