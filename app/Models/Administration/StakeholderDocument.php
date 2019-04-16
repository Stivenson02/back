<?php

namespace App\Models\Administration;

use Illuminate\Database\Eloquent\Model;

class StakeholderDocument extends Model
{
    protected $table = "stakeholder_document";
    protected $primaryKey = 'id';
    protected $fillable = ["id", "stakeholder_id", "document_id", "path", "status_document"];
}
