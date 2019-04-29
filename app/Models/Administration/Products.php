<?php

namespace App\Models\Administration;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = "products";
    protected $primaryKey = 'id';
    protected $fillable = [
        "id",
        "category_id",
        "supplier_id",
        "title",
        "description",
        "short_description",
        "reference",
        "units_supplier",
        "units_sf",
        "cost_sf",
        "tax",
        "price_sf",
        "url_part",
        "bar_code",
        "status_id",
        "meta_title",
        "meta_keywords",
        "meta_description",
        "minimum_stock",
        "characteristic",
        "account_id",
        "packaging",
        "update_id",
        "type_product_id",
        "warehouse",
        "pvp",
        "is_new",
        "about",
        "why",
        "ingredients",
        "nutritionals",
        "measures",
        "discount",
        "check_catalog"
    ];
    protected $casts = [
        'nutritionals' => 'array',
        'measures' => 'array',
    ];

    public function images() {
        return $this->hasMany(ProductsImage::class, "product_id");
    }

    public function supplier(){
        return $this->belongsTo(Stakeholder::class);
    }

    public function departureDetail() {
        return $this->belongsTo(\App\Models\Inventory\DeparturesDetail::class);
    }

    public function purchaseDetail() {
        return $this->belongsTo(\App\Models\Invoicing\PurchasesDetail::class);
    }
}
