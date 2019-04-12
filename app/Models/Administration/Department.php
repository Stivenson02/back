<?php

namespace App\Models\Administration;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = "departments";
    protected $primaryKey = "id";
    protected $fillable = ["id", "description", "code"];

    public function city() {
        return $this->belongsTo(Cities::class);
    }
}
