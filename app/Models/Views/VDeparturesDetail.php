<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class VDeparturesDetail extends Model
{
    protected $table = "vdepartures_detail";
    protected $primaryKey = 'id';

    public function VDepartures() {
        return $this->hasMany(VDepartures::class, "id", "departure_id");
    }
}
