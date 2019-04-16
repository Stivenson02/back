<?php

namespace App\Models\Administration;


use App\Administrator;
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

}
