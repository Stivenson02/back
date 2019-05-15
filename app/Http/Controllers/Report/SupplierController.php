<?php

namespace App\Http\Controllers\Report;

use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;

class SupplierController extends Controller
{

    public function getSales(Request $req){

        $this->dateActual($req);
        return  $this->getListSales($req->init, $req->end);

    }

    public function getListSales($init, $end){

        $auth= Auth::User();
        $sql = "SELECT p.id, p.title, sum(d.real_quantity * d.units_sf)::integer as quantity, 
            sum(d.real_quantity * d.value * d.units_sf ) as subtotal
            FROM departures_detail d
            JOIN departures dep ON dep.id=d.departure_id and dep.status_id IN (2,7) and client_id NOT IN(258,264,24)
            JOIN products p ON p.id = d.product_id
            JOIN stakeholder sta ON sta.id = p.supplier_id 
            AND dep.dispatched BETWEEN '" . $init . " 00:00' AND '" . $end . " 23:59'
            WHERE  dep.type_order=1  AND p.supplier_id = " .  $auth->stakeholder->id."
            GROUP BY 1, 2
            ORDER BY 3 DESC";


        return  DB::select($sql);
    }

    public function getClient(Request $req){

        $this->dateActual($req);
        return  $this->getListClient($req->init, $req->end);

    }

    public function getListClient($init, $end){

        $auth= Auth::User();
        $sql = "SELECT sta.id,sta.business, sum(d.quantity * d.units_sf) as quantity, 
                sum(d.real_quantity * d.value * d.units_sf ) as subtotal,
                sum( round(((d.real_quantity * d.value * d.units_sf) * coalesce(d.discount::float/100,0))::numeric) ) + coalesce(dep.discount::integer,0) as discount,
                sum( (d.real_quantity * d.value * d.units_sf) - round(((d.real_quantity * d.value * d.units_sf) * coalesce(d.discount::float/100,0))::numeric) ) - coalesce(dep.discount::integer,0) as subtotal_discount, 
                json_agg(DISTINCT dep.invoice) invoices
                FROM departures_detail d 
                JOIN departures dep ON dep.id=d.departure_id AND dep.status_id IN(2,7) AND dep.client_id NOT IN(258,264,24)
                JOIN products p ON p.id=d.product_id 
                JOIN stakeholder sta ON sta.id=dep.client_id 
                WHERE  dep.dispatched BETWEEN '" . $init . " 00:00' AND '" . $end . " 23:59' AND dep.type_order=1 
                AND p.supplier_id = " .  $auth->stakeholder->id."
                GROUP BY 1, dep.client_id, dep.discount
                ORDER by 3 DESC";

        return DB::select($sql);
    }

    public function getDahsSales(){

        $data=  $this->getListSales('2019-01-01', '2019-03-01');
        $data=  $sliced_array = array_slice($data, 0, 5);
        return $data;
    }

    /**
     * @param Request $req
     * @throws \Exception
     */
    public function dateActual(Request $req){

        if ($req->init == null) {
            $fecha = new DateTime();
            $fecha->modify('first day of this month');
            $req->init = $fecha->format('Y-m-d');

        }else{
            $req->init = date('Y-m-d', strtotime($req->init));
        }

        if ($req->end == null) {
            $month = date('m');
            $year = date('Y');
            $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));
            $req->end = $year . '-' . $month . '-' . $day;
        }else{
            $req->end  = date('Y-m-d', strtotime($req->end));
        }
    }

}
