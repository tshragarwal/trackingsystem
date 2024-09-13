<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddPublisherRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\CommonTrait;
use App\Http\Requests\UpdatePublisherRequest;
use App\Models\PublisherJobModel;

class PublisherController extends Controller
{
    public function index(Request $request, int $companyID) {
        $request->merge(['company_id' => $companyID]);
        $requestData = $request->all();
        $result = User::publisherList($requestData, 20);
        $result->appends($requestData);
        
        return view('publisher.list', ['data'=> $result, 'filter' => $requestData]);
    }
    
    public function create(){
       return view('publisher.create');
    }

    public function store(AddPublisherRequest $request, int $companyID){
        
        User::create([
            'company_id' => $companyID,
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'user_type' => 'publisher'
        ]);
        
        return redirect()->back()->with('success_status','Publisher Details Successfully Added.');
    }
    
    public function edit(int $companyID, int $id){

        $user =User::findOrFail($id);

        if($user->company_id !== $companyID) {
            abort(403, "Invalid request");
        }

        return view('publisher.edit', ['data' => $user, 'error' => '']);
    }
    
    public function update(UpdatePublisherRequest $request, int $companyID, int $id){
        
        $requestData = $request->post();
        $user = User::findOrFail($id);

        if($user->company_id !== $companyID) {
            abort(403, "Invalid operation");
        }
    
        if ($user->user_type != 'publisher'){
            return redirect()->back()->with('error_status','Sent details not matched with Publisher details');
        }
            
        if (($user->email != $requestData['email']) && User::where('email', $requestData['email'])->first()){
            return redirect()->back()->with('error_status','Email Already Exists with other users');
        }
            
        $user->email = $requestData['email'];
        $user->name = $requestData['name'];
        if(!empty($requestData['password'])){
            $user->password = Hash::make($requestData['password']);
        }
        $user->updated_at = date('Y-m-d H:i:s');
        $user->update();
        
        return redirect()->back()->with('success_status','Campaign data Updated Successfully');
            
    }
    
    public function destroy(int $companyID, int $id){
            
        $record = User::where(['id' => $id, 'company_id' => $companyID, 'user_type' => 'publisher']);
        if(!$record) {
            return response()->json(['message' => 'Publisher not found'], 404);
        }

        PublisherJobModel::where('publisher_id', $id)->delete();
        
        $record->delete();
        $message = 'Publisher deleted successfully';
        
        return response()->json(['message' => $message, 'status' => 1]);
        
    }    
}
