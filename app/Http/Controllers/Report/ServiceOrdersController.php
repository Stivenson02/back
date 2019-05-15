<?php

namespace App\Http\Controllers\Report;

use App\Models\Views\VDeparturesDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceOrdersController extends Controller
{
    public function getSalesProduct()
    {
        $auth= Auth::User();
        $product=$auth->stakeholder->VPurchase()
            ->select('created_at','updated_at','description','warehouse','city','status',
                DB::raw('(updated_at::date - created_at::date) AS DateDiff'))
            ->get();
        return $product;
    }

    public function getListDeparture()
    {
        $auth= Auth::User();
      return  $departures_detail= $auth->stakeholder->VDeparturesDetail()->get();

    }
}