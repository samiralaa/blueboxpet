<?php

namespace App\Http\Controllers;

use App\Models\Drugs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DrugsController extends Controller
{
    public function index()
    {
        $drugs = Drugs::with('media','city', 'user')->get();
        return response()->json($drugs);
    }

    public function filterWithName(Request $request)
    {
        $drugs = Drugs::where('name', 'like', '%' . $request->name . '%')
            ->with('media','city', 'user')->get();
        return response()->json($drugs);
    }
    public function store(Request $request)
    {
      
        // Validate the incoming request data
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'location' => 'required',
            'price' => 'nullable',
            'city_id' => 'required|exists:cities,id', // Assuming you want to ensure the city_id exists in the cities table
            'phone' => 'required',
            'quantity' => 'required|in:1,2,3', // Use the 'in' rule for specific allowed values
            'expiry_date' => 'required|date',
            'category_id' => 'required|in:1,2,3', // Use the 'in' rule for specific allowed values
            'media' => 'required|image|mimes:jpg,png,jpeg,gif|max:2048' // Validate that the file is an image
        ]);
        
        $drugData = $request->only(['name', 'description', 'location', 'category_id', 'city_id','expiry_date','phone','quantity','price']);
        if(Auth::check()){
            $userId = Auth::user()->id;
        $drugData['user_id'] = $userId; 
        }
        $drugData['status'] = 'active'; // Set the status to 'active'
        
        $drug = Drugs::create($drugData);
      
        if ($request->hasFile('media')) {
            // Retrieve the uploaded file
            $file = $request->file('media');
            
            // Check if the file is an instance of UploadedFile
            if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                // Add media to the collection
                $drug->addMedia($file)->toMediaCollection('drugs');
            } else {
                return response()->json(['message' => 'Invalid file upload'], 400);
            }
        }
        
        return response()->json($drug, 201);
    }


    public function show($drug)
    {
        $data = Drugs::where('id', $drug)
            ->with('media','city', 'user')->first();
        return response()->json($data);
    }

    public function update(Request $request,  $drug)
    {

        $drug = Drugs::find($drug);
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'location' => 'required',
            'city_id' => 'required',
            'category_id' => 'required',
            'expiry_date' => 'nullable|date'
        ]);

        if($request->hasFile('media')){
            $drug->clearMediaCollection('drugs');
            $drug->addMedia($request->file('media'))->toMediaCollection('drugs');
        }

        $drug->update($request->all());
        return response()->json($drug);
    }

    public function destroy($drug)
    {
        $data = Drugs::find($drug);
        $data->delete();
        return response()->json(null, 204);
    }

    public function updateStatus($id)
{
    $drug = Drugs::find($id);

    if (!$drug) {
        return response()->json(['error' => 'Drug not found'], 404);
    }

    $drug->status = 'inactive';
    $drug->save();

    return response()->json($drug, 200);
}




}
