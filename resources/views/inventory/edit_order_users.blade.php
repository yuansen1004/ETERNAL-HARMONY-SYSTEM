@extends('layout')

@section('content')
<style>
    .background-container {
        max-width: 800px;
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
    .order-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .slot-item {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #3498db;
        overflow: hidden;
    }
    .slot-item h4 {
        margin: 0 0 10px 0;
        color: #2c3e50;
    }
    .slot-details {
        margin-bottom: 10px;
        color: #666;
    }
    .slot-details p {
        margin: 5px 0;
    }
    /* Ensure responsive design */
    @media (max-width: 768px) {
        .background-container {
            padding: 20px;
            margin: 10px;
        }
        .slot-item {
            padding: 12px;
        }
        .form-group input, .form-group textarea {
            padding: 10px;
            font-size: 14px;
        }
    }
</style>

<div class="background-container">
    <h2>Edit User Names for Order #{{ $order->id }}</h2>
    
    <div class="order-info">
        <p><strong>Customer:</strong> {{ $customer->customer_name }}</p>
        <p><strong>Order Date:</strong> {{ $order->order_date->format('d M Y') }}</p>
        <p><strong>Total Amount:</strong> RM{{ number_format($order->total_amount, 2) }}</p>
        <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
    </div>

    <form method="POST" action="{{ route('order.update-users', $order->id) }}">
        @csrf
        
        <h3>Slots in this Order</h3>
        
        @foreach($items as $item)
            <div class="slot-item">
                <h4>
                    @if(isset($item->slot_number))
                        Slot {{ $item->slot_number }}
                    @else
                        Slot {{ $item->row }}-{{ $item->column }}
                    @endif
                </h4>
                <div class="slot-details">
                    <p><strong>Inventory:</strong> {{ $item->inventory->name ?? 'N/A' }}</p>
                    <p><strong>Company:</strong> {{ $item->inventory->company->company_name ?? 'N/A' }}</p>
                    <p><strong>Current User:</strong> {{ $item->user_name ?? 'Not assigned' }}</p>
                </div>
                
                <div class="form-group">
                    <label for="user_name_{{ $item->id }}">User Name for this Slot</label>
                    <input type="text" 
                           name="user_names[{{ $item->id }}]" 
                           id="user_name_{{ $item->id }}" 
                           value="{{ old('user_names.' . $item->id, $item->user_name) }}" 
                           placeholder="Enter the name of the person who will use this slot">
                </div>
            </div>
        @endforeach
        
        <div style="margin-top: 30px;">
            <a href="{{ route('customers.show', $customer->id) }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-submit">Update All User Names</button>
        </div>
    </form>
</div>
@endsection 