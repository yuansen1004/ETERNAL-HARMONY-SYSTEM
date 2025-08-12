<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'customer_id',
        'package_id',
        'inventory_item_id',
        'order_date',
        'payment_method',
        'payment_status',
        'installment_duration',
        'installment_paid',
        'monthly_payment',
        'total_amount',
        'receipt_details',
        'package_status',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'receipt_details' => 'array',
        'monthly_payment' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'installment_paid' => 'integer',
        'package_status' => 'string',
    ];

    // Payment status constants
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    // Package status constants
    const PACKAGE_STATUS_PENDING = 'pending';
    const PACKAGE_STATUS_COMPLETE = 'complete';

    // Payment method constants
    const METHOD_FULL_PAID = 'full_paid';
    const METHOD_INSTALLMENT = 'installment';

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function inventoryItem()
    {
        return $this->belongsTo(\App\Models\InventoryItem::class, 'inventory_item_id');
    }

    /**
     * Many-to-many relationship with inventory items
     */
    public function inventoryItems()
    {
        return $this->belongsToMany(\App\Models\InventoryItem::class, 'order_inventory_item')
                    ->withPivot('custom_price', 'original_price')
                    ->withTimestamps();
    }

    /**
     * Get all inventory items for bulk purchase orders
     */
    public function getAllInventoryItems()
    {
        // Use the many-to-many relationship if available
        if ($this->inventoryItems()->count() > 0) {
            return $this->inventoryItems;
        }
        
        // Fallback to JSON storage method
        if (isset($this->receipt_details['items']) && is_array($this->receipt_details['items'])) {
            $itemIds = collect($this->receipt_details['items'])->pluck('item_id')->toArray();
            return \App\Models\InventoryItem::whereIn('id', $itemIds)->get();
        }
        
        // Fallback to single item
        return collect([$this->inventoryItem])->filter();
    }

    /**
     * Check if this is a bulk purchase order
     */
    public function isBulkPurchase(): bool
    {
        // Check pivot table first
        if ($this->inventoryItems()->count() > 1) {
            return true;
        }
        
        // Fallback to JSON storage method
        return isset($this->receipt_details['items']) && 
               is_array($this->receipt_details['items']) && 
               count($this->receipt_details['items']) > 1;
    }

    /**
     * Get the number of items in this order
     */
    public function getItemCount(): int
    {
        // Check pivot table first
        $pivotCount = $this->inventoryItems()->count();
        if ($pivotCount > 0) {
            return $pivotCount;
        }
        
        // Fallback to JSON storage method
        if ($this->isBulkPurchase()) {
            return count($this->receipt_details['items']);
        }
        return 1;
    }

    /**
     * Check if payment is completed
     */
    public function isPaid(): bool
    {
        return $this->payment_status === self::STATUS_COMPLETED;
    }

    /**
     * Check if payment is installment
     */
    public function isInstallment(): bool
    {
        return $this->payment_method === self::METHOD_INSTALLMENT;
    }
}