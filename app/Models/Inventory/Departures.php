<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;

class Departures extends Model
{
    protected $table = "departures";
    protected $primaryKey = "id";
    protected $fillable = [
        "id",
        "responsible_id",
        "branch_id",
        "city_id",
        "client_id",
        "warehouse_id",
        "destination_id",
        "address",
        "phone",
        "status_id",
        "shipping_cost",
        "invoice",
        "description",
        "paid_out",
        "transport",
        "insert_id",
        "update_id",
        "outstanding",
        "remission",
        "discount",
        "type_request",
        "responsible",
        'insert_id',
        'type_insert_id',
        "order_id",
        "purchase_order",
        "date_appointment",
        "quantity",
        "quantity_packaging",
        "status_briefcase_id",
        "type_order"
    ];


    public function detail() {
        return $this->hasMany(DeparturesDetail::class, "departure_id");
    }

    public function cityOrigin() {
        return $this->hasOne(\App\Models\Administration\Cities::class, "id", "city_id");
    }

    public function cityDelivery() {
        return $this->hasOne(\App\Models\Administration\Cities::class, "id", "destination_id");
    }

    public function commercial() {
        return $this->hasOne(\App\Administrator::class, "id", "responsible_id");
    }

    public function client() {
        return $this->hasOne(\App\Models\Administration\Stakeholder::class, "id", "client_id");
    }

    public function warehouse() {
        return $this->hasOne(\App\Models\Administration\Warehouses::class, "id", "warehouse_id");
    }

    public function branch_office() {
        return $this->hasOne(\App\Models\Administration\Branch::class, "id", "branch_id");
    }


}
