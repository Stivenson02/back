<?php

namespace App;

use App\Models\Administration\Cities;
use App\Models\Administration\Stakeholder;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Administrator extends Authenticatable
{
    use HasApiTokens,
        Notifiable;

    protected $guard = 'admins';

    protected $table = "administrators";
    protected $primaryKey = "id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["id", "name", "last_name", "email", "city_id",
        "stakeholder_id", "role_id", "status_id",
        "password", "remember_token", "document", "warehouse_id"];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function stakeholder() {
        return $this->hasOne(Stakeholder::class,'id','stakeholder_id');
    }

    public function city() {
        return $this->hasOne(Cities::class,'id','city_id');
    }


}
