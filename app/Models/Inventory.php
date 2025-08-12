<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'company_id',
        'name',
        'category',
        'rows',
        'columns',
        'price',
        'images',
        'main_image',
        'starting_slot_number',
    ];

    public function items()
    {
        return $this->hasMany(InventoryItem::class, 'inventory_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
} 