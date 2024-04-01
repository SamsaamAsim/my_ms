<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockManagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'available_stock',
        'dropdown',
        'minimum_stock',
        'maximum_stock',
        'spare_stock',
        'minimum_stock_required',
        'maximum_stock_required',
        'our_stock'
        
    ];
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
