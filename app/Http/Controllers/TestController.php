<?php

namespace App\Http\Controllers;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class TestController extends Controller
{
    
    public function index()
    {
        ini_set('max_execution_time', 2000); // Increase max execution time
        set_time_limit(2000); // Increase the script execution time
    
        $allData = [];
        $limit = 1000;
        $skip = 0;
        $total = 0;
        $retryCount = 3; // Number of retries in case of a timeout
    
        // Initial API call to get the total count
        $response = $this->makeRequest('https://api.fda.gov/drug/label.json', $limit, $skip);
    
        if ($response->successful()) {
            $data = $response->json();
            $total = $data['meta']['results']['total']; // Total records from API
    
            do {
                // Fetch paginated data
                $response = $this->makeRequest('https://api.fda.gov/drug/label.json', $limit, $skip, $retryCount);
    
                if ($response->successful()) {
                    $data = $response->json();
                    $results = $data['results'];
    
                    foreach ($results as $item) {
                        $brandName = $item['openfda']['generic_name'][0] ?? null;
                        if ($brandName) {
                            // Store or update the brand in the database
                            Brand::updateOrCreate(
                                ['name' => $brandName],
                                [] // Add additional fields to update if necessary
                            );
                        }
                    }
    
                    // Increment the skip value to fetch the next set of results
                    $skip += $limit;
    
                } else {
                    return response()->json(['error' => 'Failed to fetch data'], 500);
                }
    
            } while ($skip < $total); // Continue until all data is fetched
        } else {
            return response()->json(['error' => 'Failed to fetch initial data'], 500);
        }
    
        return response()->json(['message' => 'All data successfully stored in the database']);
    }
    
    private function makeRequest($url, $limit, $skip, $retryCount = 3)
    {
        $response = null;
    
        for ($i = 0; $i < $retryCount; $i++) {
            $response = Http::timeout(120) // Set the timeout to 120 seconds
                            ->get($url, [
                                'limit' => $limit,
                                'skip' => $skip,
                            ]);
    
            if ($response->successful()) {
                return $response;
            }
    
            // If the request failed, wait for a short period before retrying
            sleep(2);
        }
    
        return $response;
    }
    

    

}
