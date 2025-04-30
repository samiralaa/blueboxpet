<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitiesPrice extends Model
{
    use HasFactory;

    protected $table = 'cities_price';

    protected $fillable = [
        'city_id',
        'price',
    ];

    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }
}
