<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalDetail extends Model
{
    protected $table = 'rental_details';
    protected $primaryKey = 'rental_detail_id';
    public $timestamps = false;
    protected $fillable = [
        'rental_id',
        'product_id',
        'quantity',
        'selected_size',
        'selected_color',
        'rental_days',
        'price',
        'subtotal',
        'created_at',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
