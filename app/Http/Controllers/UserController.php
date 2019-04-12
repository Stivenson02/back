<?php

namespace App\Http\Controllers;

use App\Administrator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth= Auth::User();
        $stakeolder=$auth->stakeholder()->first();
        $auth['business_name']=$stakeolder->business_name;
        $auth['business']=$stakeolder->business;
        $auth['phone_contact']=$stakeolder->phone_contact;
        $auth['send_city_id']=$stakeolder->send_city_id;
        $auth['send_city_name']=$stakeolder->sendCity()->first()->description;
        $auth['send_departament_id']=$stakeolder->sendCity()->first()->department_id;
        $auth['send_departament_name']=$stakeolder->sendCity()->first()->department()->first()->description;
        $auth['address_send']=$stakeolder->address_send;
        $auth['invoice_city_id']=$stakeolder->invoiceCity()->first()->id;
        $auth['address_invoice']=$stakeolder->address_invoice;
        $auth['invoise_city_name']=$stakeolder->invoiceCity()->first()->description;
        $auth['invoise_departament_id']=$stakeolder->invoiceCity()->first()->department_id;
        $auth['invoise_departament_name']=$stakeolder->invoiceCity()->first()->department()->first()->description;
        return $auth;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Administrator  $administrator
     * @return \Illuminate\Http\Response
     */
    public function show(Administrator $administrator)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Administrator  $administrator
     * @return \Illuminate\Http\Response
     */
    public function edit(Administrator $administrator)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Administrator  $administrator
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Administrator $administrator)
    {
        $admin =$administrator->find($request->id);
        $admin->name = $request->name;
        $admin->last_name = $request->last_name;
        $admin->phone = $request->phone;
        $stakeholder= $admin->stakeholder()->first();
        $stakeholder->phone_contact = $request->phone_contact;
        $stakeholder->send_city_id = $request->send_city_id;
        $stakeholder->address_send = $request->address_send;
        $stakeholder->invoice_city_id = $request->invoice_city_id;
        $stakeholder->address_invoice = $request->address_invoice;
        $admin->save();
        $stakeholder->save();

        return [
            "message"=>"Informacion actualizada",
            "code"=>200
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Administrator  $administrator
     * @return \Illuminate\Http\Response
     */
    public function destroy(Administrator $administrator)
    {
        //
    }
}
