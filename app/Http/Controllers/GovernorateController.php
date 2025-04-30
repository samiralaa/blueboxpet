<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Drugs;
use App\Models\Governorate;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    public function index()
    {
    $governorate = Governorate::get()
    ->select('governorate_name_ar','governorate_name_en','id');
    return response()->json($governorate);
    }

    

    public function getAllCitybyGovernorate($id)
    {
        $city = City::where('governorate_id', $id)
        ->select('city_name_ar','city_name_en','id')
        ->get();
        return response()->json($city);
    }

    public function getAllDrugsByCity( $id)
    {
        $drug = Drugs::where('city_id', $id)->get();
        return response()->json($drug);
    }

    public function getAllDrugsByCategory( $id)
    {
        $drug = Drugs::where('category_id', $id)->with('media','city', 'user')->get();
        return response()->json($drug);
    }
}
