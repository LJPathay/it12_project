<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'batch_number',
        'quantity',
        'remaining_quantity',
        'unit_price',
        'expiry_date',
        'received_date',
        'supplier',
        'notes',
        'previous_stock',
        'total_stock_after',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'received_date' => 'date',
        'unit_price' => 'decimal:2',
    ];

    /**
     * Get the inventory item this batch belongs to
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Check if batch is expired
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if batch is depleted
     */
    public function isDepleted()
    {
        return $this->remaining_quantity <= 0;
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) return null;
        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Scope for active batches
     */
    public function scopeActive($query)
    {
        return $query->where('remaining_quantity', '>', 0);
    }

    /**
     * Scope for expiring soon (within 90 days)
     */
    public function scopeExpiringSoon($query, $days = 90)
    {
        return $query->whereNotNull('expiry_date')
                    ->whereBetween('expiry_date', [now(), now()->addDays($days)]);
    }

    /**
     * Scope ordered by expiry date (FIFO)
     */
    public function scopeFifo($query)
    {
        return $query->orderBy('expiry_date', 'asc')
                    ->orderBy('received_date', 'asc');
    }
}
