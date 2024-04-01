<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartNumber extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'part_numbers',
        
    ];
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
