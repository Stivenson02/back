<?php

namespace App\Models\Administration;

use Illuminate\Database\Eloquent\Model;

class NewProductsImage extends Model
{
    protected $table = "new_products_image";
    protected $primaryKey = 'id';
    protected $fillable = ["id", "product_id", "path", "main", "thumbnail", "path_origin"];

    public function product() {
        return $this->belongsTo(NewProducts::class);
    }
}
