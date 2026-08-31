<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'total_hours',
        'discount_percentage',
        'priority_booking',
        'kit_rental',
        'special_benefits',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'priority_booking' => 'boolean',
        'kit_rental' => 'boolean',
        'special_benefits' => 'array',
        'is_active' => 'boolean',
    ];

    public function userMemberships()
    {
        return $this->hasMany(UserMembership::class);
    }
}
