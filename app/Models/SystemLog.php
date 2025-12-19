<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'loggable_type',
        'loggable_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'status',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = static::generateId();
            }
            if (empty($model->status)) {
                $model->status = 'active';
            }
        });
    }

    public static function generateId(): string
    {
        $prefix = 'BHW';
        // Get all logs with the prefix and find the highest number
        $logs = static::where('id', 'like', $prefix . '%')->get();

        $maxNumber = 0;
        foreach ($logs as $log) {
            if (preg_match('/' . preg_quote($prefix) . '(\d+)/', $log->id, $matches)) {
                $number = intval($matches[1]);
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        $newNumber = $maxNumber + 1;
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the entity that owns the log (Patient, Admin, or SuperAdmin).
     */
    public function loggable()
    {
        return $this->morphTo();
    }

    /**
     * Legacy accessor for backwards compatibility.
     */
    public function user()
    {
        return $this->loggable();
    }

    /**
     * Get professional event name based on action and context
     */
    public function getEventNameAttribute()
    {
        // Handle login/logout
        if ($this->action === 'login') return 'User Login';
        if ($this->action === 'logout') return 'User Logout';
        
        // Get table-specific event names
        if ($this->table_name === 'appointments') {
            return $this->getAppointmentEventName();
        }
        
        if ($this->table_name === 'patients') {
            if ($this->action === 'created') return 'Patient Registration';
            if ($this->action === 'updated') return 'Patient Record Updated';
            if ($this->action === 'deleted') return 'Patient Archived';
        }
        
        if ($this->table_name === 'inventories') {
            if ($this->action === 'created') return 'Inventory Item Added';
            if ($this->action === 'updated') return 'Stock Updated';
            if ($this->action === 'deleted') return 'Item Removed';
        }
        
        // Generic fallback
        $tableName = $this->getReadableTableName();
        return ucfirst($this->action) . ' ' . ucfirst($tableName);
    }

    private function getAppointmentEventName()
    {
        if ($this->action === 'created') {
            $isWalkIn = $this->new_values['is_walk_in'] ?? false;
            return $isWalkIn ? 'Walk-In Created' : 'Appointment Created';
        }
        
        if ($this->action === 'updated') {
            $newStatus = $this->new_values['status'] ?? null;
            $oldStatus = $this->old_values['status'] ?? null;
            
            // Map status transitions to intent-based events
            if ($newStatus && $oldStatus !== $newStatus) {
                return match($newStatus) {
                    'approved' => 'Appointment Approved',
                    'in_progress' => 'Appointment Started',
                    'completed' => 'Appointment Completed',
                    'cancelled' => 'Appointment Cancelled',
                    'rescheduled' => 'Appointment Rescheduled',
                    'no_show' => 'Patient No-Show',
                    default => 'Appointment Updated'
                };
            }
            
            // Check for rescheduling
            if (isset($this->new_values['appointment_date']) && 
                ($this->old_values['appointment_date'] ?? null) !== $this->new_values['appointment_date']) {
                return 'Appointment Rescheduled';
            }
            
            return 'Appointment Updated';
        }
        
        if ($this->action === 'deleted') return 'Appointment Deleted';
        
        return 'Appointment Modified';
    }

    /**
     * Get professional actor display format
     */
    public function getActorDisplayAttribute()
    {
        if (!$this->loggable) return 'System';
        
        $name = $this->loggable->name ?? 'Unknown';
        $type = class_basename($this->loggable_type);
        
        return match($type) {
            'Admin' => "Admin (staff) - {$name}",
            'SuperAdmin' => "Super Admin - {$name}",
            'Patient' => "Patient - {$name}",
            default => $name
        };
    }

    /**
     * Get target identifier (what was acted upon)
     */
    public function getTargetAttribute()
    {
        if (!$this->table_name || !$this->record_id) {
            return $this->module_name ?? 'System';
        }
        
        $tableName = $this->getReadableTableName();
        return ucfirst($tableName) . " #{$this->record_id}";
    }

    /**
     * Get contextual details for the log entry
     */
    public function getDetailsAttribute()
    {
        // For appointments, show patient and service
        if ($this->table_name === 'appointments') {
            return $this->getAppointmentDetails();
        }
        
        // For patients, show patient name
        if ($this->table_name === 'patients') {
            $name = $this->new_values['name'] ?? $this->old_values['name'] ?? null;
            if ($name) return $name;
        }
        
        // For status changes, show transition
        if (isset($this->new_values['status']) && isset($this->old_values['status'])) {
            $old = ucfirst($this->old_values['status']);
            $new = ucfirst($this->new_values['status']);
            return "{$old} → {$new}";
        }
        
        // For updates, show field count
        if ($this->action === 'updated') {
            $changedFields = count($this->new_values ?? []);
            if ($changedFields > 0) {
                return "{$changedFields} field" . ($changedFields > 1 ? 's' : '') . " changed";
            }
        }
        
        return '—';
    }

    private function getAppointmentDetails()
    {
        $patientName = $this->new_values['patient_name'] ?? $this->old_values['patient_name'] ?? null;
        $service = $this->new_values['service_type'] ?? $this->old_values['service_type'] ?? null;
        
        if ($patientName && $service) {
            return "{$service} for {$patientName}";
        }
        
        if ($patientName) {
            return "Patient: {$patientName}";
        }
        
        if ($service) {
            return "Service: {$service}";
        }
        
        // Check for date changes (rescheduling)
        if (isset($this->new_values['appointment_date'])) {
            $newDate = $this->new_values['appointment_date'];
            $oldDate = $this->old_values['appointment_date'] ?? null;
            
            if ($oldDate && $oldDate !== $newDate) {
                return "Rescheduled to {$newDate}";
            }
        }
        
        return '—';
    }

    public function getActionLabelAttribute()
    {
        return match($this->action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            default => ucfirst($this->action)
        };
    }

    public function getDescriptionAttribute()
    {
        // Handle login/logout
        if ($this->action === 'login') return "Logged into the system";
        if ($this->action === 'logout') return "Logged out of the system";
        
        // Get readable table name
        $tableName = $this->getReadableTableName();
        $recordRef = $this->record_id ? " #" . $this->record_id : "";
        
        // Build contextual description based on action and table
        switch ($this->action) {
            case 'created':
                return $this->getCreatedDescription($tableName, $recordRef);
                
            case 'updated':
                return $this->getUpdatedDescription($tableName, $recordRef);
                
            case 'deleted':
                return "Deleted {$tableName}{$recordRef}";
                
            default:
                return ucfirst($this->action) . " {$tableName}{$recordRef}";
        }
    }

    private function getReadableTableName()
    {
        if (!$this->table_name) return 'record';
        
        // Convert table names to singular, readable forms
        $readable = [
            'appointments' => 'appointment',
            'patients' => 'patient',
            'admins' => 'admin',
            'super_admins' => 'super admin',
            'inventories' => 'inventory item',
            'services' => 'service',
            'system_logs' => 'system log',
        ];
        
        return $readable[$this->table_name] ?? str_replace('_', ' ', rtrim($this->table_name, 's'));
    }

    private function getCreatedDescription($tableName, $recordRef)
    {
        // Add context for specific tables
        if ($this->table_name === 'appointments') {
            $patientName = $this->new_values['patient_name'] ?? null;
            $service = $this->new_values['service_type'] ?? null;
            
            if ($patientName && $service) {
                return "Created appointment{$recordRef} for {$patientName} ({$service})";
            }
            if ($patientName) {
                return "Created appointment{$recordRef} for {$patientName}";
            }
        }
        
        if ($this->table_name === 'patients') {
            $name = $this->new_values['name'] ?? null;
            if ($name) {
                return "Registered new patient: {$name}";
            }
        }
        
        return "Created new {$tableName}{$recordRef}";
    }

    private function getUpdatedDescription($tableName, $recordRef)
    {
        // Check for status changes
        if (isset($this->new_values['status'])) {
            $oldStatus = $this->old_values['status'] ?? 'unknown';
            $newStatus = $this->new_values['status'];
            return "Changed {$tableName}{$recordRef} status from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus);
        }
        
        // Check for appointment rescheduling
        if ($this->table_name === 'appointments' && isset($this->new_values['appointment_date'])) {
            $oldDate = $this->old_values['appointment_date'] ?? null;
            $newDate = $this->new_values['appointment_date'] ?? null;
            
            if ($oldDate && $newDate && $oldDate !== $newDate) {
                return "Rescheduled appointment{$recordRef} to {$newDate}";
            }
        }
        
        // Generic update with field count
        $changedFields = count($this->new_values ?? []);
        if ($changedFields > 0) {
            return "Updated {$tableName}{$recordRef} ({$changedFields} field" . ($changedFields > 1 ? 's' : '') . " changed)";
        }
        
        return "Updated {$tableName}{$recordRef}";
    }

    public function getModuleNameAttribute()
    {
        if (!$this->table_name) return 'System';
        
        $modules = [
            'appointments' => 'Appointments',
            'patients' => 'Patient Records',
            'admins' => 'Staff Management',
            'super_admins' => 'Admin Management',
            'inventories' => 'Inventory',
            'services' => 'Services',
            'system_logs' => 'System Logs',
        ];
        
        return $modules[$this->table_name] ?? ucwords(str_replace('_', ' ', $this->table_name));
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
