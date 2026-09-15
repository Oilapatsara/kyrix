<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'phone', 'address',
    ];

    protected $hidden = ['password'];

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'customer_id', 'customer_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id', 'customer_id');
    }
}