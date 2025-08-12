@extends('layout')

@section('content')
<style>
    .background-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    .form-group input, .form-group textarea {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }
    .form-group input:focus, .form-group textarea:focus {
        outline: none;
        border-color: #3498db;
    }
    .btn-submit {
        background-color: #3498db;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .btn-submit:hover {
        background-color: #2980b9;
    }
    .btn-cancel {
        background-color: #95a5a6;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        margin-right: 10px;
        transition: background-color 0.3s ease;
    }
    .btn-cancel:hover {
        background-color: #7f8c8d;
    }
    .slot-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .slot-info p {
        margin: 8px 0;
    }
    /* Ensure responsive design */
    @media (max-width: 768px) {
        .background-container {
            padding: 20px;
            margin: 10px;
        }
        .slot-info {
            padding: 12px;
        }
        .form-group input, .form-group textarea {
            padding: 10px;
            font-size: 14px;
        }
    }
</style>

<div class="background-container">
    <h2>Edit User Name</h2>
    
    @php
        $order = \App\Models\Order::where('inventory_item_id', $item->id)
            ->orWhereHas('inventoryItems', function($query) use ($item) {
                $query->where('inventory_item_id', $item->id);
            })
            ->first();
        $customerId = $order ? $order->customer_id : 1;
    @endphp
    
    <div class="slot-info">
        <p><strong>Slot:</strong> {{ $slot->name }} ({{ ucwords(str_replace('_', ' ', $slot->category)) }})</p>
        <p><strong>Position:</strong> 
            @if(isset($item->slot_number))
                Slot {{ $item->slot_number }}
            @else
                Row {{ $item->row }}, Column {{ $item->column }}
            @endif
        </p>
        <p><strong>Current User:</strong> {{ $item->user_name ?? 'Not assigned' }}</p>
    </div>

    <form method="POST" action="{{ route('inventory.item.update-user', $item->id) }}">
        @csrf
        <div class="form-group">
            <label for="user_name">User Name</label>
            <input type="text" name="user_name" id="user_name" value="{{ old('user_name', $item->user_name) }}" placeholder="Enter the name of the person who will use this slot">
        </div>
        
        <div style="margin-top: 30px;">
            <a href="{{ route('customers.show', $customerId) }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-submit">Update User Name</button>
        </div>
    </form>
</div>
@endsection 