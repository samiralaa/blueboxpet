<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use App\Models\Drugs;
class ReportController extends Controller
{

    public function index()
    {
        $reports = Report::all();
        return response()->json($reports);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'drug_id' => 'required',
            'description' => 'required',
        ]);

        $report = new Report();
        $report->drug_id = $request->drug_id;
        $report->description = $request->description;
        $report->save();

        return response()->json('Report submitted successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($report)
    {
        $report = Report::find($report);
        return response()->json($report);
    }


    public function edit(Report $report)
    {
        //
    }

    public function update(Request $request, Report $report)
    {
        //
    }

    public function destroy($report)
    {
        $report = Report::find($report);
        $report->delete();
        return response()->json('Report deleted successfully');
    }

    public function filter(Request $request)
    {
        // Validate incoming filter data
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'city_id' => 'nullable|int',
            'category_id' => 'nullable|int',
            'location' => 'nullable|string'
        ]);

        // Initialize query
        $query = Drugs::query();

        // Filter by creation date
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        } elseif ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        } elseif ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by city_id
        if ($request->has('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by category_id
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if($request->has('location'))

        {
            $query->where('location', 'like', '%'. $request->location. '%');

        }
        // Execute query and get results
        $drugs = $query->with('media','city', 'user')->get();

        // Return the filtered results
        return response()->json($drugs);
    }

   
}
