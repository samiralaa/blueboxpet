<?php

namespace App\Http\Controllers;

use App\Models\CitiesPrice;
use Illuminate\Http\Request;

class CitiesPriceController extends Controller
{
    public function index()
    {
        $citiesPrices = CitiesPrice::with('city')->get();
        return response()->json($citiesPrices);
    }

    public function store(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'price' => 'required|numeric|min:0',
        ]);

        $cityPrice = CitiesPrice::create($request->all());

        return response()->json($cityPrice, 201);
    }

    public function show($id)
    {
        $cityPrice = CitiesPrice::with('city')->findOrFail($id);
        return response()->json($cityPrice);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id',
            'price' => 'required|numeric|min:0',
        ]);

        $cityPrice = CitiesPrice::findOrFail($id);
        $cityPrice->update($request->all());

        return response()->json($cityPrice);
    }

    public function destroy($id)
    {
        $cityPrice = CitiesPrice::findOrFail($id);
        $cityPrice->delete();

        return response()->json(null, 204);
    }
}
