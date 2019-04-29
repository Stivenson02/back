<?php

namespace App\Models\Invoicing;

use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    protected $table='purchases';
    protected $primaryKey="id";
    protected $fillable=["id","description","warehouse_id","city_id","supplier_id","created","status_id","responsible_id","invoice"];

    public function detail() {
        return $this->hasMany(PurchasesDetail::class, "purchase_id");
    }

    public function supplier() {
        return $this->hasOne(\App\Models\Administration\Stakeholder::class, "id","supplier_id");
    }

    public function warehouse() {
        return $this->hasOne(\App\Models\Administration\Warehouses::class, "id","warehouse_id");
    }
    public function responsible() {
        return $this->hasOne(\App\Administrator::class, "id","responsible_id");
    }

}
