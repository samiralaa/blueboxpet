<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Brand;
use Illuminate\Support\Facades\Http;
class SendEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        
    
        ini_set('max_execution_time', 2000);  // 300 seconds = 5 minutes
        set_time_limit(2000);
        $allData = [];
        $limit = 1000;
        $skip = 0;
        $total = 0;
    
        do {
            $response = Http::get('https://api.fda.gov/drug/label.json', [
                'limit' => $limit,
                'skip' => $skip,
            ]);
    
            if ($response->successful()) {
                $data = $response->json();
                $total = $data['meta']['results']['total'];
                $allData = array_merge($allData, $data['results']);
                
                foreach ($data['results'] as $item) {
                    $brandName = $item['openfda']['brand_name'][0] ?? null;
                    if ($brandName) {
                        Brand::updateOrCreate(
                            ['name' => $brandName],
                       
                        );
                    }
                }
    
                $skip += $limit;
            } else {
                return response()->json(['error' => 'Failed to fetch data'], 500);
            }
        } while ($skip < $total);
    
        return response()->json(count($allData));
    }
}
