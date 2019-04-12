<?php

namespace App\Models\Administration;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    protected $table = "cities";
    protected $primaryKey = "id";
    protected $fillable = ["id", "description", "department_id", "code"];

    public function departureCity() {
        return $this->belogsTo(\App\Models\Inventory\Departures::class);
    }
    public function department() {
        return $this->hasOne(Department::class, "id", "department_id");
    }
}
