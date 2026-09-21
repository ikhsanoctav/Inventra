<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'sku',
        'type', // goods or service
        'name',
        'image',
        'category_id',
        'uom_id',
        'currency_id',
        'standard_price',
        'stock',
        'min_stock',
        'supplier_id',
        'photo_path',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
