<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportN2sModel;
use App\Models\ReportTypeinModel;
use App\Http\Traits\CommonTrait;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Models\AdvertizerRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


use League\Csv\Writer;


class ReportController extends Controller
{
    use CommonTrait;
    
    // ----- mapping like [ Csv Header Name =>  Table column ]
    protected $_n2s_csv_mapping_header = [
        'Date' => 'date', 'Country'=> 'country', 'Advertiser Subid' => 'subid', 'Searches' => 'total_searches', 'Clicks' => 'ad_clicks','CTR' => 'ctr', 'Net Revenue' => 'revenue',
        'Advertiser Name' => 'advertiser_name', 'Campaign Name' => 'campaign_name', 'Campaign id' => 'campaign_id', 'TQ' => 'tq', 'Publisher RPM' => 'publisher_RPM', 
        'Publisher RPC' => 'publisher_RPC', 'Advertiser RPM' => 'advertiser_RPM', 'Advertiser CPC' => 'advertiser_CPC', 'Gross Revenue' => 'gross_revenue', 
        'Publisher Id' => 'publisher_id', 'Offer Id' => 'offer_id'
        ];
    
    
    protected $_typein_csv_mapping_header = [
        'Date' => 'date', 'Country'=> 'country', 'Advertiser Name' => 'advertiser_name', 'Campaign Name' => 'campaign_name', 'Advertiser Subid' => 'subid', 'Campaign id' => 'campaign_id',  
        'Total Searches' => 'total_searches', 'Monetized Searches' => 'monetized_searches' ,'Ad Clicks' => 'ad_clicks','Ad Coverage' => 'ad_coverage', 'CTR' => 'ctr','CPC' => 'cpc', 
        'RPM' => 'rpm', 'Gross Revenue' => 'gross_revenue', 'Publisher Id' => 'publisher_id', 'Offer Id' => 'offer_id',
        'Publisher RPM' => 'publisher_RPM', 'Publisher RPC' => 'publisher_RPC', 'Net Revenue' => 'net_revenue',
        ];


    public function csv(){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        
        return view('reports.uploadcsv');
    }
    
    public function uploadcsv(Request $request) {
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        // Use a CSV parsing library (e.g., League\Csv) to read and import data
        $csv = \League\Csv\Reader::createFromPath($filePath);

        $i = 0;
        $batch = $firstRow = [];
        foreach ($csv->getRecords() as $record) {
            if ($i == 0){
                $firstRow = $record;
            }else{
                $batch[] = array_combine($firstRow, $record);
            }
                       
            if($i == 100) {
               $this->insertData($batch);
               $batch = [];
            }
            $i = 0;
            $i++;
        }
        if (!empty($batch)){
             $this->insertData($batch);
        }
        return redirect()->back()->with('success', 'CSV file imported successfully.');
    }
    
    private function insertData(array $batch ) : bool {
        $finalBatch = [];
        foreach($batch as $singleRecod) { 
            $newDoc = [];
            foreach($singleRecod as $fieldname => $value){
                if(isset($this->_n2s_csv_mapping_header[$fieldname])) {
                    if($fieldname == 'Date'){
//                        $date = DateTime::createFromFormat('d/m/Y', $value);
//                        $value = $date->format('Y-m-d');
                         $value = date('Y-m-d',strtotime($value)); 
                    }
                    $newDoc[$this->_n2s_csv_mapping_header[$fieldname]] = $value;
                }
            }
            $newDoc['created_at'] = date('Y-m-d H:i:s');
            $newDoc['updated_at'] = date('Y-m-d H:i:s');
            $finalBatch[] = $newDoc;
        }
        
        if(!empty($finalBatch)){
            return DB::table('report_n2s')->insert($finalBatch);
        }
        return false;
    }
    
    public function list(Request $request) {
        $requestData = $request->all();
        if(!empty($requestData['start_date'])){
             $validator = Validator::make($request->all(), [
                'start_date' => 'date|date_format:Y-m-d'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
        }
        if(!empty($requestData['end_date'])){
             $validator = Validator::make($request->all(), [
                'end_date' => 'date|date_format:Y-m-d'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
        }
        
        $publisher_advertizer_list = $this->get_advertizer_publisher_list();
        $admin = true;
        $requestData['publishers_id'] = !empty($requestData['publishers_id'])? explode(',', $requestData['publishers_id']): [];
        if(Auth::guard('web')->user()->user_type != "admin"){
            $admin = false;
            $requestData['publishers_id'] = [Auth::guard('web')->user()->id];
        }
        $requestData['advertizers_name'] = !empty($requestData['advertizers_name'])? explode(',', $requestData['advertizers_name']): [];
        
        
        $size = 50;
        $reportN2sModel = new ReportN2sModel();
        $data = $reportN2sModel->reportList($requestData, $size);
        return view('reports.list', ['data' => $data, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list, 'adminFlag' => $admin]);
    }
    
    public function n2s_downloadcsv(Request $request){
        
        $requestData = [];
        parse_str($request->all()['query_string'], $requestData);
        
        if(!empty($requestData['start_date'])){
             $validator = Validator::make($request->all(), [
                'start_date' => 'date|date_format:Y-m-d'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
        }
        if(!empty($requestData['end_date'])){
             $validator = Validator::make($request->all(), [
                'end_date' => 'date|date_format:Y-m-d'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
        }
        
        $requestData['publishers_id'] = !empty($requestData['publishers_id']) ? explode(',', $requestData['publishers_id']): [];
        $requestData['advertizers_name'] = !empty($requestData['advertizers_name']) ? explode(',', $requestData['advertizers_name']): [];
        
        $publisher = false;
        if(Auth::guard('web')->user()->user_type != "admin"){
            $publisher = true;
            $requestData['publishers_id'] = [Auth::guard('web')->user()->id];
        }

        $reportModel = new ReportN2sModel();
        $data = $reportModel->downloadcsvdata($requestData);
        $filename = "tracking_system_".time().".csv";
        
        $handle = fopen($filename, 'w+');
        
        // ------ header ---------//
        if($publisher){
            $hF = array_flip($this->_n2s_csv_mapping_header);
            fputcsv($handle, [
                $hF['advertiser_name'], $hF['gross_revenue'], $hF['publisher_id'], $hF['offer_id'], $hF['publisher_RPM'], $hF['publisher_RPC']
            ]);
        }else{
            fputcsv($handle, array_keys($this->_n2s_csv_mapping_header));
        }

        
        foreach($data as $row) {
            if($publisher) {
                fputcsv($handle, [
                    $row['advertiser_name'],$row['gross_revenue'], $row['publisher_id'], $row['offer_id'], $row['publisher_RPM'], $row['publisher_RPC']
                ]);            
            } else {
                fputcsv($handle, [
                    $row['date'], $row['country'], $row['subid'], $row['total_searches'], $row['ad_clicks'], $row['ctr'], $row['revenue'], $row['advertiser_name'], $row['campaign_name'], $row['campaign_id'],
                    $row['tq'], $row['publisher_RPM'], $row['publisher_RPC'], $row['advertiser_RPM'], $row['advertiser_CPC'],
                    $row['gross_revenue'], $row['publisher_id'], $row['offer_id']
                ]);
            }
        }
        fclose($handle);
        
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        $respone = Response::download($filename, $filename, $headers)->deleteFileAfterSend(true);
        return $respone;
    }
    
    public function typein_list(Request $request) {
        $requestData = $request->all();
        if(!empty($requestData['start_date'])){
             $validator = Validator::make($request->all(), [
                'start_date' => 'date|date_format:Y-m-d'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
        }
        if(!empty($requestData['end_date'])){
             $validator = Validator::make($request->all(), [
                'end_date' => 'date|date_format:Y-m-d'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
        }
        
        $publisher_advertizer_list = $this->get_advertizer_publisher_list();
        $admin = true;
        $requestData['publishers_id'] = !empty($requestData['publishers_id'])? explode(',', $requestData['publishers_id']): [];
        if(Auth::guard('web')->user()->user_type != "admin"){
            $admin = false;
            $requestData['publishers_id'] = [Auth::guard('web')->user()->id];
        }
        $requestData['advertizers_name'] = !empty($requestData['advertizers_name'])? explode(',', $requestData['advertizers_name']): [];
        
        $size = 50;
        $reportN2sModel = new ReportTypeinModel();
        $data = $reportN2sModel->reportList($requestData, $size);
       
        return view('reports.typein_list', ['data' => $data, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list, 'adminFlag' => $admin]);
        
    }
    
    public function typein_csv(){
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        
        return view('reports.typein_uploadcsv');
    }
    
    public function typein_uploadcsv(Request $request) {
        if(!CommonTrait::is_super_admin()){
            return view('access_denied');
        }
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        // Use a CSV parsing library (e.g., League\Csv) to read and import data
        $csv = \League\Csv\Reader::createFromPath($filePath);

        $i = 0;
        $batch = $firstRow = [];
        foreach ($csv->getRecords() as $record) {
            if ($i == 0){
                $firstRow = $record;
            }else{
                $batch[] = array_combine($firstRow, $record);
            }
           
            if($i == 100) {
               $this->typein_insertData($batch);
               $batch = [];
            }
            $i = 0;
            $i++;
        }
        if (!empty($batch)){
             $this->typein_insertData($batch);
        }
        return redirect()->back()->with('success', 'CSV file imported successfully.');
    }
    
    private function typein_insertData(array $batch ) : bool {
        $finalBatch = [];
        foreach($batch as $singleRecod) { 
            $newDoc = [];
            foreach($singleRecod as $fieldname => $value){
                if(isset($this->_typein_csv_mapping_header[$fieldname])) {
                    if($fieldname == 'Date'){
//                        $date = DateTime::createFromFormat('d/m/Y', $value);
//                        $value = $date->format('Y-m-d');
                          $value = date('Y-m-d',strtotime($value)); 
                    }
                    $newDoc[$this->_typein_csv_mapping_header[$fieldname]] = $value;
                }
            }
            $newDoc['created_at'] = date('Y-m-d H:i:s');
            $newDoc['updated_at'] = date('Y-m-d H:i:s');
            $finalBatch[] = $newDoc;
        }
        
        if(!empty($finalBatch)){
            return DB::table('report_typein')->insert($finalBatch);
        }
        return false;
    }
    
    private function get_advertizer_publisher_list(): array {
        // --- in case of superadmin -------
        $advertizer_list = [];
        $publisher_list = [];
        if(CommonTrait::is_super_admin()) {
            $allAdvertizer = AdvertizerRequest::all('name');
            $advertizer_list = $allAdvertizer->toArray();
            
            $userModel = new User();
            $publisher_list = $userModel->all_publisher_list();
            $publisher_list = $publisher_list->toArray();
        }
        
        return ['advertizer_list' => $advertizer_list, 'publisher_list' => $publisher_list];
    }
    
    public function typein_downloadcsv(Request $request){
        
        $requestData = [];
        parse_str($request->all()['query_string'], $requestData);
        
        if(!empty($requestData['start_date'])){
             $validator = Validator::make($request->all(), [
                'start_date' => 'date|date_format:Y-m-d'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
        }
        if(!empty($requestData['end_date'])){
             $validator = Validator::make($request->all(), [
                'end_date' => 'date|date_format:Y-m-d'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }
        }
        
        $requestData['publishers_id'] = !empty($requestData['publishers_id']) ? explode(',', $requestData['publishers_id']): [];
        $requestData['advertizers_name'] = !empty($requestData['advertizers_name']) ? explode(',', $requestData['advertizers_name']): [];
        
        $publisher = false;
        if(Auth::guard('web')->user()->user_type != "admin"){
            $publisher = true;
            $requestData['publishers_id'] = [Auth::guard('web')->user()->id];
        }
        $reportModel = new ReportTypeinModel();
        $data = $reportModel->downloadcsvdata($requestData);
        $filename = "tracking_system_".time().".csv";
        
        $handle = fopen($filename, 'w+');
        
        // ------ header ---------//
        if($publisher){
            $hF = array_flip($this->_typein_csv_mapping_header);
            fputcsv($handle, [
                $hF['advertiser_name'], $hF['gross_revenue'], $hF['publisher_id'], $hF['offer_id'], $hF['publisher_RPM'], $hF['publisher_RPC']
            ]);
        }else{
            fputcsv($handle, array_keys($this->_typein_csv_mapping_header));
        }
        
        foreach($data as $row) {
            if($publisher) {
                fputcsv($handle, [
                        $row['advertiser_name'], $row['gross_revenue'], $row['publisher_id'], $row['offer_id'], $row['publisher_RPM'], $row['publisher_RPC']
                        ]);                
            } else {
                fputcsv($handle, [
                    $row['date'], $row['country'], $row['advertiser_name'],$row['campaign_name'], $row['subid'],$row['campaign_id'], 
                    $row['total_searches'], $row['monetized_searches'],$row['ad_clicks'], $row['ad_coverage'], $row['ctr'], $row['cpc'],
                    $row['rpm'], $row['gross_revenue'], $row['publisher_id'], $row['offer_id'], 
                    $row['publisher_RPM'],$row['publisher_RPC'], $row['net_revenue']
                ]);
            }
        }
        fclose($handle);
        
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        $respone = Response::download($filename, $filename, $headers)->deleteFileAfterSend(true);
        return $respone;
    }
    
    public function n2s_csv_sample(){
        $filename = "tracking_system_n2s_csv_sample.csv";
        
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array_keys($this->_n2s_csv_mapping_header));
      
        fclose($handle);
        
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        $respone = Response::download($filename, $filename, $headers)->deleteFileAfterSend(true);
        return $respone;
    }
    public function typein_csv_sample(){
        $filename = "tracking_system_typein_csv_sample.csv";
        
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array_keys($this->_typein_csv_mapping_header));
      
        fclose($handle);
        
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        $respone = Response::download($filename, $filename, $headers)->deleteFileAfterSend(true);
        return $respone;
    }
}
