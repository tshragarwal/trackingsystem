<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportN2sModel;
use App\Models\ReportTypeinModel;
use App\Http\Traits\CommonTrait;
use App\Models\Advertiser;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use League\Csv\Writer;


class ReportController extends Controller
{
    use CommonTrait;

    public function index(Request $request, int $companyID, string $type = 'n2s') {
        $request->merge(['company_id' => $companyID]);
        
        $request->validate([
            'start_date' => ['nullable','date', 'date_format:Y-m-d'],
            'end_date' => ['nullable','date', 'date_format:Y-m-d'],
        ]);
        $requestData = $request->all();
        
        
        $publisher_advertizer_list = $this->get_advertizer_publisher_list($companyID);
        $admin = true;
        $requestData['publishers_id'] = !empty($requestData['publishers_id'])? explode(',', $requestData['publishers_id']): [];
        if(Auth::guard('web')->user()->user_type != "admin"){
            $admin = false;
            $requestData['publishers_id'] = [Auth::guard('web')->user()->id];
        }
        $requestData['advertizers_name'] = !empty($requestData['advertizers_name'])? explode(',', $requestData['advertizers_name']): [];
        
        
        $size = 100;
        $data = [];
        $view = 'reports.n2s.list';
        if($type === 'n2s') {
            $data = ReportN2sModel::reportList($requestData, $size);
        } else if($type === 'typein') {
            $view = 'reports.typein.list';
            $data = ReportTypeinModel::reportList($requestData, $size);
        }

        return view($view, ['data' => $data, 'query_string' => $request->query(), 'publisher_advertizer_list' => $publisher_advertizer_list, 'adminFlag' => $admin]);
    }

    public function upload(int $companyID, string $type) {
        $view = 'reports.n2s.upload';
        if($type === 'typein') {
            $view = 'reports.typein.upload';
        }
        return view($view);
    }

    public function downloadSampleCSV(int $companyID, string $type) {
        $filename = "N2S-Sample-CSV.csv";
        $csvHeaders = CommonTrait::n2s_csv_mapping_header();

        if($type === 'typein'){
            $filename = "TypeIN-Sample-CSV.csv";
            $csvHeaders = CommonTrait::typein_csv_mapping_header();
        }
        
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array_keys($csvHeaders));
        fclose($handle);
        
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        return  Response::download($filename, $filename, $headers)->deleteFileAfterSend(true);
    }

    public function store(Request $request, int $companyID, string $type) {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        $headers = CommonTrait::n2s_csv_mapping_header();
        if($type === 'typein') {
            $headers = CommonTrait::typein_csv_mapping_header();
        }

        // Use a CSV parsing library (e.g., League\Csv) to read and import data
        $csv = \League\Csv\Reader::createFromPath($filePath);

        $i = 0;
        $firstRow = $csv->first();
        $foundDiff = array_diff(array_keys($headers), $firstRow);
        
        if(!empty($foundDiff)) {
            throw ValidationException::withMessages(['csv_file' => 'Incorrect file headers.' . implode(",", $foundDiff)]);
        }


        $batch = [];
        foreach ($csv->getRecords() as $record) {
            if ($i == 0){
                $i++;
                continue;
            }else{
                $batch[] = array_combine($firstRow, $record);
            }
            $i++;
                       
            if($i == 100) {
               $this->insertData($batch, $companyID, $type);
               $batch = [];
               $i = 1;
            }
        }
        if (!empty($batch)){
             $this->insertData($batch, $companyID, $type);
        }
        return redirect()->back()->with('success', 'CSV file imported successfully.');
    }
    
    private function insertData(array $batch, int $companyID, string $type ) : bool {
        $finalBatch = [];
        $headers = CommonTrait::n2s_csv_mapping_header();
        if($type === 'typein') {
            $headers = CommonTrait::typein_csv_mapping_header();
        }

        foreach($batch as $singleRecod) { 
            $newDoc = [];
            foreach($singleRecod as $fieldname => $value){
                if(isset($headers[$fieldname])) {
                    if($fieldname == 'Date'){
//                        $date = DateTime::createFromFormat('d/m/Y', $value);
//                        $value = $date->format('Y-m-d');
                         $value = date('Y-m-d',strtotime($value)); 
                    }
                    $newDoc[$headers[$fieldname]] = $value;
                }
            }
            $newDoc['company_id'] = $companyID;
            $newDoc['created_at'] = date('Y-m-d H:i:s');
            $newDoc['updated_at'] = date('Y-m-d H:i:s');
            $finalBatch[] = $newDoc;
        }

        if(!empty($finalBatch)){
            if($type === 'n2s') {
                return DB::table('report_n2s')->upsert($finalBatch, ['company_id','date', 'subid', 'campaign_id', 'publisher_id'],
                    ['company_id', 'advertiser_name', 'publisher_name', 'offer_id', 'campaign_name', 'country', 'total_searches', 'ad_clicks', 'ctr', 'tq', 'revenue', 'publisher_RPM',
                        'publisher_RPC', 'advertiser_RPM', 'advertiser_CPC', 'gross_revenue','updated_at']);
            } else if($type === 'typein') {
                return DB::table('report_typein')->upsert($finalBatch, 
                    ['company_id', 'date', 'subid', 'campaign_id', 'publisher_id'],
                    ['company_id', 'advertiser_name', 'publisher_name', 'campaign_name', 'country', 'total_searches','monetized_searches', 'ad_clicks','ad_coverage', 
                        'ctr', 'cpc', 'rpm','gross_revenue','offer_id', 'publisher_RPM', 'publisher_RPC','net_revenue','updated_at']);
            }
        }
        return false;
    }

    public function flushTable(int $companID, string $type) {
        try{
            if($type === 'n2s') {
                $record = ReportN2sModel::where(['company_id' => $companID])->delete();
            } else if ($type === 'typein') {
                $record = ReportTypeinModel::where(['company_id' => $companID])->delete();
            }
            if ($record) {
                return response()->json(['message' => 'Data Deleted', 'status' => 1], 200);
            } else {
                return response()->json(['message' => 'Data Not Deleted'], 400);
            }
        } catch(Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }        
    }
    
    public function edit(Request $request, int $companyID, string $type, int $id) {
        $view = 'reports.n2s.edit';
        if($type === 'n2s') {
            $data = ReportN2sModel::where(['id' => $id, 'company_id' => $companyID])->first();
        } else if ($type === 'typein') {
            $view = 'reports.typein.edit';
            $data = ReportTypeinModel::where(['id' => $id, 'company_id' => $companyID])->first();
        } else {
            abort(404, 'Invalid report type');
        }

        if($data->company_id !== $companyID) {
            abort(400, "Bad request");
        }

        if(empty($data)){
            return ['error' => 'Invalid Request'];
        }
        return view($view, ['data' => $data]);
    }

    public function update(Request $request, int $companyID, string $type, int $id) {
        $data = $request->all();
        unset($data['_token']);

        if($type === 'n2s') {
            $record = ReportN2sModel::findOrFail($id);
        } else if($type === 'typein') {
            $record = ReportTypeinModel::findOrFail($id);
        } else {
            abort(404, 'Invalid report type');
        }
        if($record->company_id !== $companyID) {
            abort(400, "Bad request");
        }
        $response = $record->update($data);
        
        if($response) {
            return redirect()->route('report.list', ['company_id' => $companyID, 'type' => $type])->with('success_status', 'Report successfully Updated.');
        }
    }

    public function destroy(int $companyID, string $type, int $id) {
        if($type === 'n2s') {
            $data = ReportN2sModel::where(['id' => $id, 'company_id' => $companyID])->first();
        } else if ($type === 'typein') {
            $view = 'reports.typein.edit';
            $data = ReportTypeinModel::where(['id' => $id, 'company_id' => $companyID])->first();
        } else {
            abort(404, 'Invalid report type');
        }

        if(!empty($data)){

            $response = $data->delete();
            if ($response) {
                $message = 'Record deleted successfully';
                return response()->json(['message' => $message, 'status' => 1]);
            } else {
                return response()->json(['message' => 'Unable to delete record.'], 500);
            }
        }
         else {
            return response()->json(['message' => 'Record not found'], 404);
        }
    }


    
    public function n2s_downloadcsv(Request $request, int $companyID){
        
        $requestData = [];
        parse_str($request->all()['query_string'], $requestData);
        
        $request->validate([
            'start_date' => ['nullable','date', 'date_format:Y-m-d'],
            'end_date' => ['nullable','date', 'date_format:Y-m-d'],
        ]);
        $requestData = $request->all();
        
        $requestData['company_id'] = $companyID;
        
        $requestData['publishers_id'] = !empty($requestData['publishers_id']) ? explode(',', $requestData['publishers_id']): [];
        $requestData['advertizers_name'] = !empty($requestData['advertizers_name']) ? explode(',', $requestData['advertizers_name']): [];
        
        $publisher = false;
        if(Auth::guard('web')->user()->user_type != "admin"){
            $publisher = true;
            $requestData['publishers_id'] = [Auth::guard('web')->user()->id];
        }

        $data = ReportN2sModel::downloadcsvdata($requestData);
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

    private function get_advertizer_publisher_list(int $companyID): array {
        // --- in case of superadmin -------
        $advertizer_list = [];
        $publisher_list = [];
        if(CommonTrait::is_super_admin()) {
            $allAdvertizer = Advertiser::select('id', 'name')->where('company_id', $companyID)->get();
            $advertizer_list = $allAdvertizer->toArray();
            
            $userModel = new User();
            $publisher_list = $userModel->all_publisher_list($companyID);
            $publisher_list = $publisher_list->toArray();
        }
        
        return ['advertizer_list' => $advertizer_list, 'publisher_list' => $publisher_list];
    }
    
    public function typein_downloadcsv(Request $request, int $companyID){
        
        $requestData = [];
        parse_str($request->all()['query_string'], $requestData);

        $request->validate([
            'start_date' => ['nullable','date', 'date_format:Y-m-d'],
            'end_date' => ['nullable','date', 'date_format:Y-m-d'],
        ]);
        
        $requestData['company_id'] = $companyID;
        $requestData['publishers_id'] = !empty($requestData['publishers_id']) ? explode(',', $requestData['publishers_id']): [];
        $requestData['advertizers_name'] = !empty($requestData['advertizers_name']) ? explode(',', $requestData['advertizers_name']): [];

        $publisher = false;
        if(Auth::guard('web')->user()->user_type != "admin"){
            $publisher = true;
            $requestData['publishers_id'] = [Auth::guard('web')->user()->id];
        }
        $data = ReportTypeinModel::downloadcsvdata($requestData);
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
}
