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
        $n2s_csv_mapping_header = CommonTrait::n2s_csv_mapping_header();
        foreach($batch as $singleRecod) { 
            $newDoc = [];
            foreach($singleRecod as $fieldname => $value){
                if(isset($n2s_csv_mapping_header[$fieldname])) {
                    if($fieldname == 'Date'){
//                        $date = DateTime::createFromFormat('d/m/Y', $value);
//                        $value = $date->format('Y-m-d');
                         $value = date('Y-m-d',strtotime($value)); 
                    }
                    $newDoc[$n2s_csv_mapping_header[$fieldname]] = $value;
                }
            }
            $newDoc['created_at'] = date('Y-m-d H:i:s');
            $newDoc['updated_at'] = date('Y-m-d H:i:s');
            $finalBatch[] = $newDoc;
        }
        
        if(!empty($finalBatch)){
            return DB::table('report_n2s')->upsert($finalBatch, ['date', 'subid', 'campaign_id', 'publisher_id'],
                    ['advertiser_name', 'publisher_name', 'offer_id', 'campaign_name', 'country', 'total_searches', 'ad_clicks', 'ctr', 'tq', 'revenue', 'publisher_RPM',
                        'publisher_RPC', 'advertiser_RPM', 'advertiser_CPC', 'gross_revenue','updated_at']);
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
        
        
        $size = 1000;
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
        $n2s_csv_mapping_header = CommonTrait::n2s_csv_mapping_header();
        $hF = array_flip($n2s_csv_mapping_header);
        if($publisher){
            fputcsv($handle, [
               $hF['date'],$hF['offer_id'], $hF['country'],  $hF['total_searches'], $hF['ad_clicks'], $hF['ctr'],  $hF['publisher_RPC'] .' ($)',  $hF['publisher_RPM'].' ($)', $hF['revenue'].' ($)', $hF['tq']
            ]);
        }else{
            fputcsv($handle, [
                $hF['date'], $hF['advertiser_name'],  $hF['campaign_name'], $hF['campaign_id'], $hF['subid'], $hF['total_searches'], $hF['ad_clicks'], $hF['tq'], $hF['ctr'], 
                $hF['advertiser_CPC'].' ($)', $hF['advertiser_RPM'].' ($)', $hF['gross_revenue'].' ($)',  $hF['publisher_name'], $hF['publisher_id'], $hF['offer_id'],
                $hF['publisher_RPM'].' ($)', $hF['publisher_RPC'].' ($)',  $hF['revenue'].' ($)', $hF['country']
            ]);
        }

        
        foreach($data as $row) {
            if($publisher) {
                fputcsv($handle, [
                    $row['date'],$row['offer_id'], $row['country'],  $row['total_searches'], $row['ad_clicks'], $row['ctr'], '$ '. $row['publisher_RPC'],  '$ '.$row['publisher_RPM'], '$ '.$row['revenue'], $row['tq']
                ]);            
            } else {
                fputcsv($handle, [
                    $row['date'], $row['advertiser_name'],  $row['campaign_name'], $row['campaign_id'], $row['subid'], $row['total_searches'], $row['ad_clicks'], $row['tq'], $row['ctr'], 
                    '$ '.$row['advertiser_CPC'], '$ '.$row['advertiser_RPM'], '$ '.$row['gross_revenue'],  $row['publisher_name'], $row['publisher_id'], $row['offer_id'],
                    '$ '.$row['publisher_RPM'], '$ '.$row['publisher_RPC'],  '$ '.$row['revenue'], $row['country']
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
        $typein_csv_mapping_header = CommonTrait::typein_csv_mapping_header();
        foreach($batch as $singleRecod) { 
            $newDoc = [];
            foreach($singleRecod as $fieldname => $value){
                if(isset($typein_csv_mapping_header[$fieldname])) {
                    if($fieldname == 'Date'){
//                        $date = DateTime::createFromFormat('d/m/Y', $value);
//                        $value = $date->format('Y-m-d');
                          $value = date('Y-m-d',strtotime($value)); 
                    }
                    $newDoc[$typein_csv_mapping_header[$fieldname]] = $value;
                }
            }
            $newDoc['created_at'] = date('Y-m-d H:i:s');
            $newDoc['updated_at'] = date('Y-m-d H:i:s');
            $finalBatch[] = $newDoc;
        }
        
        if(!empty($finalBatch)){
            return DB::table('report_typein')->upsert($finalBatch, 
                    ['date', 'subid', 'campaign_id', 'publisher_id'],
                    ['advertiser_name', 'publisher_name', 'campaign_name', 'country', 'total_searches','monetized_searches', 'ad_clicks','ad_coverage', 
                        'ctr', 'cpc', 'rpm','gross_revenue','offer_id', 'publisher_RPM', 'publisher_RPC','net_revenue','updated_at']);
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
        $typein_csv_mapping_header = CommonTrait::typein_csv_mapping_header();
        $hF = array_flip($typein_csv_mapping_header);
        if($publisher){
            fputcsv($handle, [
                $hF['date'], $hF['offer_id'], $hF['country'] , $hF['total_searches'], $hF['monetized_searches'],
                $hF['ad_clicks'],$hF['ad_coverage'],  $hF['ctr'],    $hF['publisher_RPC'].' ($)', $hF['publisher_RPM'].' ($)', $hF['net_revenue'].' ($)'
            ]);
        }else{
            fputcsv($handle, [
                $hF['date'], $hF['advertiser_name'],  $hF['campaign_name'], $hF['campaign_id'], $hF['subid'], $hF['total_searches'], 
                $hF['monetized_searches'],
                $hF['ad_clicks'],$hF['ad_coverage'],  $hF['ctr'], $hF['cpc'].' ($)', $hF['rpm'].' ($)', $hF['gross_revenue'].' ($)',
                $hF['publisher_name'], $hF['publisher_id'], $hF['offer_id'],
                $hF['publisher_RPM'].' ($)', $hF['publisher_RPC'].' ($)',  $hF['net_revenue'].' ($)', $hF['country']
            ]);
        }
        
        foreach($data as $row) {
            if($publisher) {
                fputcsv($handle, [
                        $row['date'], $row['offer_id'], $row['country'] , $row['total_searches'], $row['monetized_searches'],
                $row['ad_clicks'],$row['ad_coverage'],  $row['ctr'],    '$ '.$row['publisher_RPC'], '$ '.$row['publisher_RPM'], '$ '.$row['net_revenue']
                        ]);                
            } else {
                fputcsv($handle, [
                    $row['date'], $row['advertiser_name'],  $row['campaign_name'], $row['campaign_id'], $row['subid'], $row['total_searches'], 
                    $row['monetized_searches'],
                    $row['ad_clicks'], $row['ad_coverage'],  $row['ctr'], '$ '.$row['cpc'], '$ '.$row['rpm'], '$ '.$row['gross_revenue'],
                    $row['publisher_name'], $row['publisher_id'], $row['offer_id'],
                    '$ '.$row['publisher_RPM'], '$ '.$row['publisher_RPC'],  '$ '.$row['net_revenue'], $row['country']
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
        $n2s_csv_mapping_header = CommonTrait::n2s_csv_mapping_header();
        fputcsv($handle, array_keys($n2s_csv_mapping_header));
      
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
        $typein_csv_mapping_header = CommonTrait::typein_csv_mapping_header();
        fputcsv($handle, array_keys($typein_csv_mapping_header));
      
        fclose($handle);
        
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        $respone = Response::download($filename, $filename, $headers)->deleteFileAfterSend(true);
        return $respone;
    }
    
    public function n2s_report_edit(Request $request){
        if(empty($request['id'])){
            return ['error' => 'invalid request'];
        }
        
        $doc = ReportN2sModel::where('id', $request['id'])->first();
        if(empty($doc)){
            return ['error' => 'Invalid Request'];
        }
       
        return view('reports.n2s_report_edit', ['data' => $doc]);
    }
    
    public function n2s_report_edit_save(Request $request){
        $data = $request->all();
        $id = $data['id'];
        unset($data['_token']);
        unset($data['id']);
        
        $response = ReportN2sModel::where('id', $id)->update($data);
        if($response) {
            return redirect()->route('report.list')->with('success_status', 'Report successfully Updated.');
        }
    }

    
    public function typein_report_edit(Request $request){
        if(empty($request['id'])){
            return ['error' => 'invalid request'];
        }
        
        $doc = ReportTypeinModel::where('id', $request['id'])->first();
        if(empty($doc)){
            return ['error' => 'Invalid Request'];
        }
       
        return view('reports.typein_report_edit', ['data' => $doc]);
    }
    
    public function typein_report_edit_save(Request $request) {
        $data = $request->all();
        $id = $data['id'];
        unset($data['_token']);
        unset($data['id']);
        
        $response = ReportTypeinModel::where('id', $id)->update($data);
        if($response) {
            return redirect()->route('report.typein_list')->with('success_status', 'Report successfully Updated.');
        }
    }
    
    
    public function delete_n2s_report_row(Request $request){
        $requestData = $request->all();

        if(!empty($requestData['id'])){
            
            $record = ReportN2sModel::find($requestData['id']);
            if ($record) {
              
                $record->delete();
                $message = 'N2S Report Row successfully deleted';
                
                return response()->json(['message' => $message, 'status' => 1]);
            } else {
                return response()->json(['message' => 'Typein Report Row not found'], 404);
            }
        }
    }      
    
    public function delete_typein_report_row(Request $request){
        $requestData = $request->all();

        if(!empty($requestData['id'])){
            
            $record = ReportTypeinModel::find($requestData['id']);
            if ($record) {
              
                $record->delete();
                $message = 'Typein Report Row successfully deleted';
                
                return response()->json(['message' => $message, 'status' => 1]);
            } else {
                return response()->json(['message' => 'Typein Report Row not found'], 404);
            }
        }
    }   
    
    public function delete_n2s_report_all(){
        $record = ReportN2sModel::truncate();
        if ($record) {
            $message = 'Data Deleted';
            return response()->json(['message' => $message, 'status' => 1]);
        } else {
            return response()->json(['message' => 'Data Not Deleted'], 404);
        }
    }  
    
    public function delete_typein_report_all(){
        $record = ReportTypeinModel::truncate();
        if ($record) {
            $message = 'Data Deleted';
            return response()->json(['message' => $message, 'status' => 1]);
        } else {
            return response()->json(['message' => 'Data Not Deleted'], 404);
        }
    }      
}
