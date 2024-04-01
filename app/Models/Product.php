<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'compatible_machine',
        'ready_to_sell',
        'additional_info',
        'sku',
        'title'

    ];
    public function images() {
        return $this->hasMany(Image::class);
    }
    public function partNumber() {
        return $this->hasMany(PartNumber::class);
    }
    public function competitor() {
        return $this->hasMany(Competitor::class);
    }
    public function priceCalculation()
    {
        return $this->hasOne(PriceCalculation::class);
    }
    public function stockManagement()
    {
        return $this->hasOne(StockManagement::class);
    }


}
