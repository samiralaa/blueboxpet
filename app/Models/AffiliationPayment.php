<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliationPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'due',
        'user_id',
        'order_id',
        'complated'
    ];
    
     
        
          public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
