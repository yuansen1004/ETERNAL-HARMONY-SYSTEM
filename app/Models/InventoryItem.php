<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $table = 'inventory_items';

    protected $fillable = [
        'inventory_id',
        'row',
        'column',
        'slot_number',
        'user_name',
        'status',
        'image',
        'occupant_name',
        'images',
    ];

    public function inventory()
    {
        return $this->belongsTo(\App\Models\Inventory::class, 'inventory_id');
    }

    /**
     * Many-to-many relationship with orders
     */
    public function orders()
    {
        return $this->belongsToMany(\App\Models\Order::class, 'order_inventory_item')
                    ->withPivot('custom_price', 'original_price')
                    ->withTimestamps();
    }
} 