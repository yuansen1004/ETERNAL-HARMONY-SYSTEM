@extends('layout')
@section('content')
    <style>
        .item-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .item-image {
            margin: 20px 0;
            text-align: center;
        }
        .item-image img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .slot-images {
            margin: 20px 0;
        }
        .slot-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            margin: 10px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status-available { color: #28a745; font-weight: bold; }

        .status-sold { color: #dc3545; font-weight: bold; }
        .btn-submit {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #f3f4f6;
            color: #848b96;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 15px;
            text-align: center;
        }
        .btn-submit:hover {
            background-color: #a5a6a8;
            color: #191c21;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }
        .btn-secondary {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #e0e0e0;
            color: #555;
            border: none;
            border-radius: 6px;
            cursor: not-allowed;
            font-size: 18px;
            font-weight: 600;
            margin-top: 15px;
            text-align: center;
        }
    </style>
    <div class="background-container">
        <h2>Slot Item Detail</h2>
        <div class="item-info">
            <p><strong>Slot:</strong> {{ $slot->name }} ({{ ucwords(str_replace('_', ' ', $slot->category)) }})</p>
            <p><strong>Position:</strong> 
                @if(isset($item->slot_number))
                    Slot {{ $item->slot_number }}
                @else
                    Row {{ $item->row }}, Column {{ $item->column }}
                @endif
            </p>
            @php
                $rowPrices = is_string($slot->row_prices) ? json_decode($slot->row_prices, true) : $slot->row_prices;
                $price = isset($rowPrices[$item->row - 1]) ? $rowPrices[$item->row - 1] : null;
            @endphp
            @if($price !== null)
                <p><strong>Price:</strong> RM{{ number_format($price, 2) }}</p>
            @endif
            <p><strong>Status:</strong> <span class="status-{{ $item->status }}">{{ ucfirst($item->status) }}</span></p>
            @if($item->user_name)
                <p><strong>User:</strong> {{ $item->user_name }}</p>
            @endif
            @if($item->occupant_name)
                <p><strong>Occupant:</strong> {{ $item->occupant_name }}</p>
            @endif
        </div>
        @if($item->image)
            <div class="item-image">
                <h4>Main Image</h4>
                <img src="{{ asset($item->image) }}" alt="Main Image" />
            </div>
        @endif
        @php
            $additionalImages = [];
            if ($item->images) {
                $additionalImages = json_decode($item->images, true);
            }
        @endphp
        @if(!empty($additionalImages))
            <div class="slot-images">
                <h4>Additional Images</h4>
                @foreach($additionalImages as $image)
                    <img src="{{ asset($image) }}" alt="Additional Image" class="slot-image" />
                @endforeach
            </div>
        @endif
        <div style="margin-top: 30px;">
            @if($item->status === 'available')
                <a href="{{ route('inventory.purchase.form', $item->id) }}" class="btn-submit">Purchase</a>
            @else
                <button class="btn-secondary" disabled>Not Available</button>
            @endif
            

        </div>
    </div>
@endsection 