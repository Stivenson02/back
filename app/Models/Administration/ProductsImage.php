<?php

namespace App\Models\Administration;

use Illuminate\Database\Eloquent\Model;

class ProductsImage extends Model
{
    protected $table = "products_image";
    protected $primaryKey = 'id';
    protected $fillable = ["id", "product_id", "path", "main", "thumbnail", "path_origin"];

    public function product() {
        return $this->belongsTo(Products::class);
    }
}
