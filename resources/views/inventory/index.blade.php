@extends('layout')
@section('content')
<div class="background-container">
    <div class="header-row">
        <h2>Inventory</h2>
        @if(Auth::check() && Auth::user()->role === 'staff')
        <a href="{{ route('inventory.slot.create') }}" class="inventory-btn">+ Add Slot</a>
        @endif
    </div>
    <div>
        <div class="inventory-section-title">Categories</div>
        <div class="inventory-list">
            @foreach($categories as $cat)
                <form method="GET" action="{{ route('inventory.category', $cat) }}">
                    <button type="submit" class="inventory-card">
                        {{ ucwords(str_replace('_', ' ', $cat)) }}
                    </button>
                </form>
            @endforeach
        </div>
    </div>
    <div>
        <div class="inventory-section-title">Companies</div>
        <div class="inventory-list">
            @foreach($companies as $company)
                <form method="GET" action="{{ route('inventory.company', $company->id) }}">
                    <button type="submit" class="inventory-card">
                        {{ $company->company_name }}
                    </button>
                </form>
            @endforeach
        </div>
    </div>
    <div>
        <div class="inventory-section-title">All Slots</div>
        <div class="inventory-list slots-grid">
            @foreach($slots as $slot)
                @php
                    $slotImage = null;
                    // Get the first item from this slot to display its main image
                    $firstItem = \App\Models\InventoryItem::where('inventory_id', $slot->id)->first();
                    if ($firstItem && $firstItem->image) {
                        $slotImage = $firstItem->image; // Main image from first item
                    } elseif ($firstItem && $firstItem->images) {
                        $images = json_decode($firstItem->images, true);
                        if (is_array($images) && !empty($images)) {
                            $slotImage = $images[0]; // First additional image
                        }
                    }
                @endphp
                <a href="{{ route('inventory.slot', $slot->id) }}" class="slot-card">
                    @if($slotImage)
                        <img src="{{ asset($slotImage) }}" 
                             alt="{{ $slot->name }}" 
                             class="slot-image">
                    @endif
                    <div class="slot-info">
                        <span class="slot-name">
                            {{ $slot->name }}
                        </span>
                        <div class="slot-details">
                            {{ ucwords(str_replace('_', ' ', $slot->category)) }}, 
                            Company: {{ $companies->firstWhere('id', $slot->company_id)->company_name ?? 'N/A' }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection 