<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ReportN2sModel;
use App\Models\ReportTypeinModel;
use App\Http\Requests\PublisherTokenDataRequest;
use App\Http\Requests\RnMatriksAPIDataRequest;
use App\Http\Resources\PublihserTokenN2SJson;
use App\Http\Traits\CommonTrait;
use Illuminate\Support\Facades\Response;
use App\Models\TrackingPublisherJobModel;


class PublisherTokenController extends Controller
{
    use CommonTrait;
    
    public function publisher_token_list(int $companyID){
        $user = Auth::guard('web')->user();
        if($user->user_type == 'admin'){
             $user = false;
        }
        $domain = "https://" . env('SEARCHOSS_API_DOMAIN') . "/publisher/token/data?token={{$user->api_token}}&start_date=2023-10-10&end_date=2023-10-10&report_type=n2s&format=json";
        if($companyID === 2 ) {
            $domain = "https://" . env('RNMATRIKS_API_DOMAIN') . "/rnmat-data/" . $user->api_token . "?start_date=2024-01-10&end_date=2024-02-29&report_type=n2s&format=json";
        }


        return view('publisher_token.token_list', ['user' => $user, 'domain' => $domain]);
    } 
    
    public function publisher_token_generate(int $companyID){
        $user = Auth::guard('web')->user();
      
        if($user->user_type == 'publisher'){
            do {
                $api_token = $this->token();
                $existsToken = User::where('api_token', $api_token)->first();
            }while(!empty($existsToken));

            $user->api_token = $api_token;
            $user->save();
            
            return redirect()->back()->with(['success_status' => 'Successfully Generated Publisher Api Token']);
            
        }
    }
    
    private function token(){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz01abhdyk23456789';
        $randomString = '';

        for ($i = 0; $i < 4; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
    public function rnmatriksAPIData(RnMatriksAPIDataRequest $request, string $token) {
        $request = $request->merge(['token' => $token]);
        return $this->getDataForAPI($request->all());
    }
    
    public function publisher_token_data(PublisherTokenDataRequest $request){
        $requestData = $request->all();
        return $this->getDataForAPI($request->all());
    }

    private function getDataForAPI(array $requestData) {
        if (!in_array($requestData['report_type'], ['n2s', 'typein'])){
            return response()->json(['message' => "Invalid Request vlaue in report_type. we support only n2s and typein"]);
        }else if (!in_array($requestData['format'], ['json', 'csv'])){
            return response()->json(['message' => "Invalid Request vlaue in format. we support only json and csv"]);
        }
        
        $user = User::where('api_token', $requestData['token'])->first();
        $requestData['company_id'] = $user->company_id;

        if(empty($user) || $user->user_type == 'admin'){
            return response()->json(['message' => "Invalid Request token."]);
        }
        
        $requestData['publishers_id'] = [$user->id];
        if(!empty($requestData['start_date'])){
            $requestData['start_date'] = date('Y-m-d', strtotime($requestData['start_date']));
        }
        if(!empty($requestData['end_date'])){
            $requestData['end_date'] = date('Y-m-d', strtotime($requestData['end_date']));
        }
        
        if($requestData['format'] == 'json'){
            if($requestData['report_type'] == 'n2s'){
                return $this->n2sreport($requestData, 'json');
            }else if($requestData['report_type'] == 'typein'){
                return $this->typeinreport($requestData, 'json');
            }
        }
        else{
            if($requestData['report_type'] == 'n2s'){
                return $this->n2sreport($requestData, 'csv');
            }else if($requestData['report_type'] == 'typein'){
                return $this->typeinreport($requestData, 'csv');
            }
        }
    }
    
    private function n2sreport($requestData, $type = 'json') {
        $n2s_csv_mapping_header = CommonTrait::n2s_csv_mapping_header();
        $hF = array_flip($n2s_csv_mapping_header);
        
                
        $reportData = ReportN2sModel::downloadcsvdata($requestData);
        
        if($type == 'csv'){
            $filename = "report_n2s_".time().".csv";
            $handle = fopen($filename, 'w+');
            fputcsv($handle, [
                    $hF['date'], $hF['offer_id'], $hF['country'], 'Total Searches', 'Ad Clicks', $hF['ctr'], 'RPC', 'RPM', $hF['revenue'],$hF['tq']
            ]);
        }
        
        $finalFieldArray = [];
        if(!empty($reportData)){
            foreach($reportData as $sRep){
                
                if($type == 'csv'){
                   fputcsv($handle, [
                      $sRep['date'], $sRep['offer_id'],  $sRep['country'],   $sRep['total_searches'],  $sRep['ad_clicks'],  $sRep['ctr'],   
                      $sRep['publisher_RPC'],   $sRep['publisher_RPM'], $sRep['revenue'], $sRep['tq']
                   ]);
                  
                }else{
                    // ---for json ------//
                    $finalFieldArray[] = [
                        $hF['date'] => $sRep['date'], 
                        $hF['offer_id'] => $sRep['offer_id'], 
                        $hF['country'] => $sRep['country'],  
                        'Total Searches' => $sRep['total_searches'], 
                        'Ad Clicks' => $sRep['ad_clicks'], 
                        $hF['ctr'] => $sRep['ctr'],  
                        'RPC' => $sRep['publisher_RPC'],  
                        'RPM' => $sRep['publisher_RPM'], 
                        $hF['revenue'] => $sRep['revenue'], 
                        $hF['tq'] => $sRep['tq']
                        ];
                }
            }
            
            if($type == 'csv'){
                fclose($handle);
        
                $headers = array(
                    'Content-Type' => 'text/csv',
                );
                $respone = Response::download($filename, $filename, $headers)->deleteFileAfterSend(true);
                return $respone;
             }    
        }
        
        return ['status' =>'success', 'data' => $finalFieldArray];
    }
    
    private function typeinreport($requestData, $type = 'json') {
        $typein_csv_mapping_header = CommonTrait::typein_csv_mapping_header();
        $hF = array_flip($typein_csv_mapping_header);
        
                
        $reportData = ReportTypeinModel::downloadcsvdata($requestData);
        
        if($type == 'csv'){
            $filename = "report_typein_".time().".csv";
            $handle = fopen($filename, 'w+');
            fputcsv($handle, [
                    $hF['date'], $hF['offer_id'], $hF['country'], $hF['total_searches'],$hF['monetized_searches'], $hF['ad_clicks'], $hF['ad_coverage'], $hF['ctr'], $hF['publisher_RPC'],  $hF['publisher_RPM'],  $hF['net_revenue']
            ]);
        }
        
        
        $finalFieldArray = [];
        if(!empty($reportData)){
            foreach($reportData as $sRep){
                if($type == 'csv'){
                   fputcsv($handle, [
                      $sRep['date'], $sRep['offer_id'], $sRep['country'], $sRep['total_searches'],$sRep['monetized_searches'], $sRep['ad_clicks'], $sRep['ad_coverage'], $sRep['ctr'], $sRep['publisher_RPC'],  $sRep['publisher_RPM'],  $sRep['net_revenue']
                   ]);
                  
                }else{
                    // ---for json ------//
                    $finalFieldArray[] = [
                        $hF['date'] => $sRep['date'], 
                        $hF['offer_id'] => $sRep['offer_id'], 
                        $hF['country'] => $sRep['country'] , 
                        $hF['total_searches'] => $sRep['total_searches'], 
                        $hF['monetized_searches'] => $sRep['monetized_searches'],
                        $hF['ad_clicks'] => $sRep['ad_clicks'],
                        $hF['ad_coverage'] => $sRep['ad_coverage'],  
                        $hF['ctr'] => $sRep['ctr'],    
                        'RPC' => $sRep['publisher_RPC'], 
                        'RPM' => $sRep['publisher_RPM'], 
                        $hF['net_revenue'] => $sRep['net_revenue']
                        ];
                }
            }
            
            if($type == 'csv'){
                fclose($handle);
        
                $headers = array(
                    'Content-Type' => 'text/csv',
                );
                $respone = Response::download($filename, $filename, $headers)->deleteFileAfterSend(true);
                return $respone;
             }  
        }
        
        return ['status' =>'success', 'data' => $finalFieldArray];
    }
    
    public function lead_verify(Request $request) {
        $data = $request->all();
       
        if(!empty($data['clkid'])){
            $trackingId = base64_decode($data['clkid']);
          
            $response = TrackingPublisherJobModel::where('id', $trackingId)->first();
            if(!empty($response)){
               $response->update(['lead_verify' => 1]);
               return response()->json(['message' => 'Successfully Processed.'], 200);
            }
        }
        return response()->json(['message' => 'Inactive Job.'], 406);
    }
}
