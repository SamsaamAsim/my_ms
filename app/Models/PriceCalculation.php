<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceCalculation extends Model
{
    protected $fillable = [
        'product_id',
       
       'buying',
       'selling',
       'add_rate',
      'catigory_rate',
       'value_fee',
      'postage',
        'add_rate_ans',
        'add_rate_gst',
        'total_add_rate',
       'catigory_rate_ans',
        'catigory_rate_gst',
       'catigory_add_rate',
        'ebay_expenses',
        'earning_from_ebay',
        'gst_on_earning',
       'earning_in_hand',
       'total_cost',
       'profit',
       'profit_margin'

    ];
    use HasFactory;
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
