<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'day_of_week',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'appointment_service');
    }
}


