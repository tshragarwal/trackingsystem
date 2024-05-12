<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AdvertiserRequest;
use App\Models\Advertiser;
use App\Models\AdvertiserCampaignModel;
use App\Http\Traits\CommonTrait;
use App\Models\PublisherJobModel;


class AdvertizerController extends Controller
{
    use CommonTrait;

    public function index(Request $request, int $companyID){
        $request->merge(['companyID' => $companyID]);
        $requestData = $request->all();
        $userObj = new Advertiser();
        
        $advertizerList = $userObj->get_publisher_list($requestData, 20);
       
        return view('advertiser.list', ['data' => $advertizerList, 'success' => $request->s, 'filter' => $requestData ]);
    }

    public function create() {
        return view('advertiser.create');
    }

    public function store(AdvertiserRequest $request, int $companyID) {
        Advertiser::create([
            'name' => $request->get('name'),
            'manual_email' => $request->get('manual_email') ?? '',
            'company_id' => $companyID
        ]);

        return redirect()->route('advertiser.list', ['company_id' => $companyID]);
    }

    public function edit(int $companyID, int $id) {
        $advertiser = Advertiser::findOrFail($id);

        if($advertiser->company_id !== $companyID) {
            abort(403, "Invalid operation");
        }
        return view('advertiser.edit', ['advertiser' => $advertiser]);
    }

    public function update(AdvertiserRequest $request, int $companyID, int $id) {
        $advertiser = Advertiser::findOrFail($id);

        if($advertiser->company_id !== $companyID) {
            abort(403, "Invalid operation");
        }

        $advertiser->name = $request->get('name');
        $advertiser->manual_email = $request->get('manual_email');
        $advertiser->save();

        return redirect()->route('advertiser.list', ['company_id' => $companyID]);
    }

    public function destroy(int $companyID, int $id){     
        $record = Advertiser::find($id);
        if(empty($record)) {
            return response()->json(['message' => 'Advertizer not found'], 404);
        }

        if($record->company_id !== $companyID) {
            return response()->json(['message' => 'Invalid operation'], 403);
        }

            
            // -- check is there campaign assign to it or not ------//
        $modelObj = new AdvertiserCampaignModel();
        $count = $modelObj->get_advertizer_campaign_count($id, $companyID);
        $s = 1;
        if( $count > 0){
            $message = "Avertizer can not be deleted because $count campaign is assigned it advertizer. Please first delete all associated campaign then Advertizer. ";
            $s = 0;
        }else{
            $record->delete();
            $message = 'Advertizer deleted successfully';
        }
        
        
        return response()->json(['message' => $message, 'status' => $s]);
        
    }
    
}
