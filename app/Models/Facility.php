<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'dimensions',
        'duration_minutes',
        'base_price_weekday',
        'base_price_weekend',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_price_weekday' => 'decimal:2',
        'base_price_weekend' => 'decimal:2',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function interests()
    {
        return $this->hasMany(Interest::class);
    }
}
