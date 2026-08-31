<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mobile',
        'email',
        'facility_id',
        'interested_date',
        'interested_time',
        'status',
    ];

    protected $casts = [
        'interested_date' => 'date',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
