<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'performable_type',
        'performable_id',
        'transaction_type',
        'quantity',
        'previous_expiry_date',
        'notes'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * Get the entity that performed the transaction (Admin or SuperAdmin).
     */
    public function performable()
    {
        return $this->morphTo();
    }

    /**
     * Legacy accessor for backwards compatibility.
     */
    public function user()
    {
        return $this->performable();
    }

    public function scopeRestock($query)
    {
        return $query->where('transaction_type', 'restock');
    }

    public function scopeUsage($query)
    {
        return $query->where('transaction_type', 'usage');
    }
}
