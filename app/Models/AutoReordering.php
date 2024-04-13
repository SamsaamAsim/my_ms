<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoReordering extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'reorder',
        'total_stock_owned',
        'spare_still_achiveable'
        

    ];
    public function product() {
        return $this->belongsTo(Product::class);
    }
    
}
