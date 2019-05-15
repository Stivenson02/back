<?php

namespace App\Models\Administration;


use App\Administrator;
use App\Models\Views\VDeparturesDetail;
use App\Models\Views\VProduts;
use App\Models\Views\VPurchases;
use Illuminate\Database\Eloquent\Model;

class Stakeholder extends Model
{

    protected $table = "stakeholder";
    protected $primaryKey = "id";


    public function User() {
        return $this->belongsTo(Administrator::class, "responsible_id");
    }

    public function invoiceCity() {
        return $this->hasOne(Cities::class,'id','invoice_city_id');
    }

    public function sendCity() {
        return $this->hasOne(Cities::class,'id','send_city_id');
    }

    public function Product() {
        return $this->hasMany(Products::class, "supplier_id");
    }

    public function VProduct() {
        return $this->hasMany(VProduts::class, "supplier_id");
    }

    public function VPurchase() {
        return $this->hasMany(VPurchases::class, "supplier_id");
    }

    public function VDeparturesDetail() {
        return $this->hasMany(VDeparturesDetail::class, "supplier_id");
    }

    public function departureClient() {
        return $this->belongsTo(\App\Models\Inventory\Departures::class);
    }

    public function documents()
    {
        return $this->hasMany(StakeholderDocument::class, "stakeholder_id");
    }

}
