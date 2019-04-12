<?php

namespace App\Http\Controllers\General;

use App\Models\Administration\Cities;
use App\Models\Administration\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GeneralController extends Controller
{
    public function getCities($id)
    {
        return Cities::where('department_id', $id)->get();
    }

    public function getDepartament()
    {
        return Department::all();
    }
}
