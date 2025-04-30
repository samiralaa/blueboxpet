<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        "name_en",
         "name_ar",
        'description',
        'category_id',
        'quantity',
        'weight',
        'type',
        'sku',
        'affiliationprice',
        'pricea',
        'priceb',
        'pricec',
        'price',
        'expiration_data',
    ];

   
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
