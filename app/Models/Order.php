<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'address',
        'phone_number',
        'another_phone',
        'item',
        'user_id',
        "duenow",
        'quantity',
        'reason',
        'price',
        'another_phone',
        'order_status',
        'delivery_fees',
        'product_id',
        'note',
    ];
    
      protected $casts = [
        'item' => 'array',
        'quantity'=>'array',
        'price'=>'array',
        'product_id'=>'array',
        ];
        
          public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function pyments()
    {
                return $this->hasMany(AffiliationPayment::class, 'order_id');

    }
    
}
