<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;



class CompanyController extends Controller
{
   
    public function selection() {
        return view('company.selection', ['companies' => Company::all()]);
    }
    
}
