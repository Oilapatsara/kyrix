<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $table = 'rentals';
    protected $primaryKey = 'rental_id';

    protected $fillable = [
        'rental_code', 'customer_id', 'rental_date', 'start_date', 'end_date',
        'total_amount', 'deposit_amount', 'service_type', 'service_fee',
        'delivery_method', 'delivery_address', 'recipient_phone',
        'tracking_number', 'return_tracking_no', 'status', 'note',
    ];

    // rentals.customer_id -> customers.customer_id
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    // rentals.rental_id -> rental_details.rental_id
    public function details()
    {
        return $this->hasMany(RentalDetail::class, 'rental_id', 'rental_id');
    }

    // Alias for details (for compatibility across controllers)
    public function rentalDetails()
    {
        return $this->details();
    }

    // rentals.rental_id -> payments.rental_id
    public function payments()
    {
        return $this->hasMany(Payment::class, 'rental_id', 'rental_id');
    }

    // rentals.rental_id -> payments.rental_id (latest payment)
    public function latestPayment()
    {
        return $this->hasOne(Payment::class, 'rental_id', 'rental_id')->latestOfMany('payment_id');
    }

    // rentals.rental_id -> reviews.rental_id
    public function reviews()
    {
        return $this->hasMany(Review::class, 'rental_id', 'rental_id');
    }
}