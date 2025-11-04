<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'size',
        'product_type',
        'measurement_type',
        'panjang_bahu',
        'panjang_lengan',
        'lingkar_dada',
        'panjang_baju',
        'lingkar_pinggang',
        'panjang_celana',
        'lingkar_paha',
        'lingkar_betis',
        'lingkar_lutut',
        'lingkar_kaki',
        'catatan',
        'image',
        'status',
        'total_price',
        'customer_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
