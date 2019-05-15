<?php

namespace App\Http\Controllers\General;

use App\Models\Administration\Categories;
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

    public function getCategories()
    {
        $info = array();
        $data= Categories::where('type_category_id',1)->orderBy('id')->get();
        $i=0;
        foreach ($data as $detal){
            if ($i == 0){
                array_push($info,array(
                    'description'=>"Selecciona una categoria",
                    'id'=>0,
                ));
            }else{
                array_push($info,array(
                    'description'=>$detal->description,
                    'short_description'=>$detal->short_description,
                    'type_category_id'=>$detal->type_category_id,
                    'slug'=>$detal->slug,
                    'id'=>$detal->id,
                ));
            }
            $i=$i+1;
        }

        return $info;
    }
}
