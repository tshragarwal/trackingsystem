<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportModel;
use App\Http\Traits\CommonTrait;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;


use League\Csv\Writer;


class ReportController extends Controller
{
    use CommonTrait;
    
    // ----- mapping like [ Csv Header Name =>  Table column ]
    protected $_csv_mapping_header = ['subid' => 'subid', 'Total Searches' => 'total_searches', 'monetized Searches' => 'monetized_searches', 'ad_clicks' => 'ad_clicks',
            'date' => 'date', 'ctr' => 'ctr', 'cpc' => 'cpc', 'rpm' => 'rpm', 'revenue (USD)' => 'revenue'];


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
                if(isset($this->_csv_mapping_header[$fieldname])) {
                    if($fieldname == 'date'){
                        $date = DateTime::createFromFormat('d/m/y', $value);
                        $value = $date->format('Y-m-d');
                    }
                    $newDoc[$this->_csv_mapping_header[$fieldname]] = $value;
                }
            }
            $newDoc['created_at'] = date('Y-m-d H;i:s');
            $newDoc['updated_at'] = date('Y-m-d H;i:s');
            $finalBatch[] = $newDoc;
        }
        
        if(!empty($finalBatch)){
            return DB::table('reports')->insert($finalBatch);
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
        
        $size = 50;
        $reportModel = new ReportModel();
        $data = $reportModel->reportList($requestData, $size);
        return view('reports.list', ['data' => $data, 'query_string' => $request->query()]);
    }
    
    public function downloadcsv(Request $request){
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
        
        $reportModel = new ReportModel();
        $data = $reportModel->downloadcsvdata($requestData);

        $filename = "tracking_system_".time().".csv";
        
        $handle = fopen($filename, 'w+');

        fputcsv($handle, array_keys($this->_csv_mapping_header));

        foreach($data as $row) {
            fputcsv($handle, [
                $row['subid'], $row['total_searches'], $row['monetized_searches'], $row['ad_clicks'], 
                $row['date'], $row['ctr'], $row['cpc'], $row['rpm'], $row['revenue']
            ]);
        }

        fclose($handle);
        
        
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        $respone = Response::download($filename, $filename, $headers)->deleteFileAfterSend(true);
        return $respone;
    }
}
