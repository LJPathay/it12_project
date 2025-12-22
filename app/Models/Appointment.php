<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
use App\Traits\Syncable;

class Appointment extends Model
{
    use HasFactory, Syncable;

    protected $fillable = [
        'patient_id',
        'patient_name',
        'patient_phone',
        'patient_address',
        'appointment_date',
        'appointment_time',
        'service_type',
        'status',
        'notes',
        'medical_history',
        'is_walk_in',
        'priority',
        'approved_by_admin_id',
        'approved_by_super_admin_id',
        'approved_at'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
        'approved_at' => 'datetime',
        'is_walk_in' => 'boolean',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'appointment_service');
    }

    /**
     * Get the patient that owns the appointment.
     */
    public function patient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Patient::class, 'patient_id');
    }

    /**
     * Legacy accessor for backwards compatibility.
     */
    public function user()
    {
        return $this->patient();
    }

    /**
     * Get the admin who approved the appointment.
     */
    public function approvedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'approved_by_admin_id');
    }

    /**
     * Get the super admin who approved the appointment.
     */
    public function approvedBySuperAdmin()
    {
        return $this->belongsTo(SuperAdmin::class, 'approved_by_super_admin_id');
    }

    /**
     * Get the approver (either admin or super admin).
     */
    public function approvedBy()
    {
        return $this->approvedByAdmin ?? $this->approvedBySuperAdmin;
    }

    /**
     * Get the approver relationship dynamically.
     */
    public function getApproverAttribute()
    {
        if ($this->approved_by_admin_id) {
            return $this->approvedByAdmin;
        }
        if ($this->approved_by_super_admin_id) {
            return $this->approvedBySuperAdmin;
        }
        return null;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today()->toDateString());
    }
}
