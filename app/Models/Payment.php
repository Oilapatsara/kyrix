<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    protected $fillable = [
        'rental_id',
        'payment_amount',
        'payment_date',
        'payment_method',
        'slip_image',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'payment_amount' => 'decimal:2',
        ];
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    public function getSlipUrlAttribute()
    {
        if (!$this->slip_image) return null;
        if (str_starts_with($this->slip_image, 'http://') || str_starts_with($this->slip_image, 'https://')) {
            return $this->slip_image;
        }
        return asset('storage/' . $this->slip_image);
    }
}
