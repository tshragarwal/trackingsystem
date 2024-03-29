<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrackingKeywordModel;
use App\Models\TrackingPublisherJobModel;
use DB;


class LocationProcessData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process_location:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $trackRecord = TrackingKeywordModel::select('ip','id')->where('campaign_id','>', 0)->where('geo_location_updated', 0)->orderBy('id', 'desc')->limit(3000)->get();
        dd($trackRecord);
        $ipData = $idData = [];
        if($trackRecord->count()){
            $chunksRecord = $trackRecord->chunk(80);
                
            foreach($chunksRecord as $chunk) {
                $ipData = $idData = [];
                foreach($chunk as $data) {
                    $ipData[] = $data->ip;
                    $idData[] = $data->id;
                }

                $ipDetail = $this->getIPDetails($ipData);
                if($this->filterIAndInsertpData($ipDetail)){
                     TrackingPublisherJobModel::whereIn('id', $idData)->update(['geo_location_updated' => 1]);
                }
            }
        }
        return;
    }
    
    
        
    private function getIPDetails($ips){
        $endpoint = 'http://ip-api.com/batch';

        $options = [
                'http' => [
                        'method' => 'POST',
                        'user_agent' => 'Batch-Example/1.0',
                        'header' => 'Content-Type: application/json',
                        'content' => json_encode($ips)
                ]
        ];
        $response = file_get_contents($endpoint, false, stream_context_create($options));

        // Decode the response and print it
        $array = json_decode($response, true);
        return $array;
    }
    
    private function filterIAndInsertpData($ipDetail){
        if(!empty($ipDetail)){
            
            $geoData = [];
            foreach($ipDetail as $data){
                $geo_location = [];
                if($data['status'] == 'success') {
                    $geo_location['ip'] = $data['query'];
                    $geo_location['country'] = $data['country'];
                    $geo_location['countryCode'] = $data['countryCode'];
                    $geo_location['region'] = $data['region'];
                    $geo_location['regionName'] = $data['regionName'];
                    $geo_location['city'] = $data['city'];
                    $geo_location['zip'] = $data['zip'];
                    $geo_location['lat'] = $data['lat'];
                    $geo_location['lon'] = $data['lon'];
                    $geo_location['timezone'] = $data['timezone'];
                    
                    $geoData[] = $geo_location;
                }
            }
            if(!empty($geoData)){
                DB::table('geo_location')->insertOrIgnore($geoData);
                return true;
            }
            
        }
        return false;
    }    
}
