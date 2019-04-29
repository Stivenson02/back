<?php

namespace App\Models\Administration;

use Illuminate\Database\Eloquent\Model;

class Warehouses extends Model
{

    protected $table = "warehouses";
    protected $primaryKey = "id";
    protected $fillable = ["id", "description", 'address', "city_id", "responsible_id"];
    public $timestamp = false;

    public function departure() {
        return $this->belongsTo(\App\Models\Inventory\Departures::class);
    }

    public function user() {
        return $this->hasOne(\App\Administrator::class, "id", "responsible_id");
    }
    public function city() {
        return $this->hasOne(Cities::class, "id", "city_id");
    }

}
