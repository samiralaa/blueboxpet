<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Brand;
use Illuminate\Support\Facades\Http;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and update brand data from FDA API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting DemoCron command...');

        ini_set('max_execution_time', 2000);  // Increase execution time
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
                $total = $data['meta']['results']['total'] ?? 0;
                $allData = array_merge($allData, $data['results']);
                
                foreach ($data['results'] as $item) {
                    $brandName = $item['openfda']['brand_name'][0] ?? null;
                    if ($brandName) {
                        Brand::updateOrCreate(
                            ['name' => $brandName],
                            // You might want to add other fields here if needed
                        );
                    }
                }

                $skip += $limit;
                $this->info("Processed $skip of $total records...");
            } else {
                $this->error('Failed to fetch data from API');
                return 1; // Return non-zero exit code to indicate failure
            }
        } while ($skip < $total);

        $this->info('Command completed successfully.');
        $this->info('Total records processed: ' . count($allData));

        return 0; // Return zero exit code to indicate success
    }
}
