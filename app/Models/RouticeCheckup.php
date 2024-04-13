<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouticeCheckup extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'note_h',
        'note_k',
        'checked_on',
        'check_again',
        'check_again_dropdown',
        'resent_update'

    ];
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
