<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvacueeCheckIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'emergency_alert_id',
        'name',
        'matric_no',
        'status',
        'latitude',
        'longitude',
        'device_id',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function emergencyAlert()
    {
        return $this->belongsTo(EmergencyAlert::class);
    }
}
