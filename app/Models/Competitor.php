<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'competitor_selling_price',
         'postage',
         'total',
         'ourless',
         'profit_competitors',
         'profit_margin_competitors',
         'competitors_name',
          'dropdown',
         '30_day_sale',
         '90_day_sale',
         '6_month_sale',
         '1_year_sale',
         '3_year_sale',
         

    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
