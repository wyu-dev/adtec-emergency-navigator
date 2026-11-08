<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'type',
        'severity',
        'title',
        'message',
        'triggered_by',
        'triggered_at',
        'resolved_at',
        'assembly_point_id',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function assemblyPoint()
    {
        return $this->belongsTo(AssemblyPoint::class);
    }

    public static function getActiveAlert()
    {
        return self::with('assemblyPoint')
            ->where('status', 'ACTIVE')
            ->latest('triggered_at')
            ->first();
    }
}
