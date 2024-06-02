<?php

namespace App\Http\Controllers;

use App\Models\RnmatriksPublisherJobModel;
use Illuminate\Http\Request;
use App\Http\Requests\CampaignSaveRequest;
use App\Models\Advertiser;
use App\Models\AdvertiserCampaignModel;
use App\Http\Traits\CommonTrait;
use App\Models\PublisherJobModel;
use App\Models\TrackingPublisherJobModel;
use DB;


class CampaignController extends Controller
{
    use CommonTrait;

    public function index(Request $request, int $companyID){
        $request->merge(['companyID' => $companyID]);
        $requestData = $request->all();
        
        $modelObj = new AdvertiserCampaignModel();
        $result = $modelObj->list($requestData, 20);
        return view('campaign.list', ['data' => $result, 'success' => $request->s??0, 'filter' => $requestData]);
    }

    public function list(Request $request, int $companyID, int $advertiserID) {
        if(!$request->ajax()) {
            return response()->json(['message' => "Invalid request"], 403);
        }
        $filter = [
            'companyID' => $companyID,
            'advertizer' => $advertiserID
        ];
        
        $campaign = new AdvertiserCampaignModel();
        $result = $campaign->list($filter);
        return response()->json($result, 200); // view('campaign.list', ['data' => $result, 'success' => $request->s??0, 'filter' => $requestData]);
    }

    public function create(int $companyID) {
        $advertisers = Advertiser::where('company_id', $companyID)->get();
        return view('campaign.create', ['advertiserObj'=> $advertisers]);
    }

    public function store(CampaignSaveRequest $request, int $companyID) {
        $requestData = $request->post();
        
            
        $tableObj = new AdvertiserCampaignModel();
        $tableObj->company_id = $companyID;
        $tableObj->advertiser_id = $requestData['advertiser_id'];
        $tableObj->campaign_name = $requestData['campaign_name'];
        if(!empty($requestData['subid'])){
            $tableObj->subid = $requestData['subid'];
        }
        $tableObj->link_type = $requestData['link_type'];
        $tableObj->target_url = $requestData['target_url'];
//                $tableObj->query_string = $requestData['query_string'];
        $tableObj->target_count = $requestData['target_count'];
        $tableObj->enable_referer_redirection = $requestData['enable_referer_redirection'] ?? 1;
        $tableObj->allow_mobile = $requestData['allow_mobile'] ?? 1;
        $tableObj->allow_tablet = $requestData['allow_tablet'] ?? 1;
        $tableObj->allow_desktop = $requestData['allow_desktop'] ?? 1;

        $tableObj->save();

        return redirect()->back()->with('success_status','Campaign Details Added Successfully');
    }

    public function edit(int $companyID, int $id) {
        $campaign = AdvertiserCampaignModel::with('advertiser')
                    ->where('id',$id)->first();

        if($campaign->company_id !== $companyID) {
            abort(403, "Invalid operation");
        }

        return view('campaign.edit', ['data' => $campaign, 'error' => '']);
    }

    public function update(CampaignSaveRequest $request, int $companyID, int $id) {
        $advertiser = AdvertiserCampaignModel::findOrFail($id);

        if($advertiser->company_id !== $companyID) {
            abort(403, "Invalid operation");
        }
        $requestData = $request->post();

        
        $advertiser->target_url = $requestData['target_url'];
        $advertiser->campaign_name = $requestData['campaign_name'];
        if(!empty($requestData['subid'])){
            $advertiser->subid = $requestData['subid'];
        }
        $advertiser->link_type = $requestData['link_type'];
        
//                $dbObj->query_string = $requestData['query_string'];
        $advertiser->target_count = $requestData['target_count'];
        $advertiser->status = $requestData['status'];

        $advertiser->enable_referer_redirection = $requestData['enable_referer_redirection'];
        $advertiser->allow_mobile = $requestData['allow_mobile'];
        $advertiser->allow_tablet = $requestData['allow_tablet'];
        $advertiser->allow_desktop = $requestData['allow_desktop'];
        $advertiser->update();
        
        return redirect()->back()->with('success_status','Campaign data Updated Successfully');
    }

    public function statusUpdate(Request $request, int $companyID, int $id) {
        if($request->ajax()){
            $campaign = AdvertiserCampaignModel::findOrFail($id);
            $requestData = $request->all();

            if($campaign->company_id !== $companyID) {
                return response()->json(['message' => "Invalid operation"], 403);
            }

            if(!in_array($requestData['status'], ['enable_referer_redirection', 'allow_mobile', 'allow_tablet', 'allow_desktop'])) {
                return response()->json(['message' => "Bad request"], 400);
            }
            
            switch($requestData['status']) {
                case 'enable_referer_redirection': 
                    $campaign->enable_referer_redirection = !$campaign->enable_referer_redirection;
                    break;
                case 'allow_mobile':
                    $campaign->allow_mobile = !$campaign->allow_mobile;
                    break;
                case 'allow_tablet':
                    $campaign->allow_tablet = !$campaign->allow_tablet;
                    break;
                case 'allow_desktop':
                    $campaign->allow_desktop = !$campaign->allow_desktop;
                    break;
                default:
            }
            $campaign->save();
            return response()->json(['message' => "Updated successfully"], 200);
        }
        abort(403, "Invalid operation");
    }

    public function destroy(int $companyID, int $id){
            
        $record = AdvertiserCampaignModel::find($id);
        if(!$record) {
            return response()->json(['message' => 'Campaing not found.'], 404);
        }

        if($record->company_id !== $companyID) {
            return response()->json(['message' => 'Invalid operation'], 403);
        }
            
        // -- check is there campaign assign to it or not ------//
        $modelObj = new PublisherJobModel();
        $count = $modelObj->get_all_campaign_publisher_job_count($id);
        $s = 1;
        if( $count > 0){
            $message = "Campaign can not be deleted because $count Publisher Job is assigned with this Campaign. Please firstly delete all associated Publisher Job.";
            $s = 0;
        }else{
            $record->delete();
            $message = 'Campaign deleted successfully';
        }
        
        return response()->json(['message' => $message, 'status' => $s]);
    }
   
    public function sync_geolocation(Request $request, int $companyID){
        ini_set('max_execution_time', 0);
        if($companyID === 1) {
            return $this->syncTrackingGeoLocation($request, $companyID);
        } else if ($companyID === 2) {
            return $this->syncRNMatriksGeoLocation($request, $companyID);
        }
        return response()->json(['message' => 'Invalid operations.'], 403);
    }

    private function syncTrackingGeoLocation(Request $request, int $companyID) {
        $requestData = $request->all();
        if(!empty($requestData['campaign_id'])){
           
            $record = AdvertiserCampaignModel::where(['id' => $requestData['campaign_id'], 'company_id' => $companyID]);
            if(!$record) {
                return response()->json(['message' => 'Campaing not found.'], 404);
            }

            if($record->company_id !== $companyID) {
                return response()->json(['message' => 'Invalid operation'], 403);
            }
            // -- check is there campaign assign to it or not ------//
            $modelObj = new TrackingPublisherJobModel();
            $records = $modelObj->get_non_sync_geo_location($requestData['campaign_id']);
            $chunksRecord = $records->chunk(80);
            
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
            
            return response()->json(['message' => "Geo Location Updated", 'status' => 1]);
        }
        return response()->json(['message' => "Invalid campaign ID.", 'status' => 0], 403);
    }

    private function syncRNMatriksGeoLocation(Request $request, int $companyID) {
        $requestData = $request->all();
        if(!empty($requestData['campaign_id'])){
           
            $record = AdvertiserCampaignModel::where(['id' => $requestData['campaign_id'], 'company_id' => $companyID]);
            if(!$record) {
                return response()->json(['message' => 'Campaing not found.'], 404);
            }

            if($record->company_id !== $companyID) {
                return response()->json(['message' => 'Invalid operation'], 403);
            }
            // -- check is there campaign assign to it or not ------//
            $modelObj = new RnmatriksPublisherJobModel();
            $records = $modelObj->get_non_sync_geo_location($requestData['campaign_id']);
            $chunksRecord = $records->chunk(80);
            
            foreach($chunksRecord as $chunk) {
                $ipData = $idData = [];
                foreach($chunk as $data) {
                    $ipData[] = $data->ip;
                    $idData[] = $data->id;
                }
                
                $ipDetail = $this->getIPDetails($ipData);
                if($this->filterIAndInsertpData($ipDetail)){
                    RnmatriksPublisherJobModel::whereIn('id', $idData)->update(['geo_location_updated' => 1]);
                }
            }
            
            return response()->json(['message' => "Geo Location Updated", 'status' => 1]);
        }
        return response()->json(['message' => "Invalid campaign ID.", 'status' => 0], 403);
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
