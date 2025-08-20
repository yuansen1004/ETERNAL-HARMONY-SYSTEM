@extends('layout')
@section('content')
<style>
    .category-filter {
        background: #e9ecef;
        padding: 10px 15px;
        border-radius: 20px;
        display: inline-block;
        font-size: 14px;
        color: #495057;
    }
    .slot-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .slot-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .slot-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
    }
    .slot-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }
    .slot-content {
        padding: 15px;
    }
    .slot-name {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        text-decoration: none;
        display: block;
    }
    .slot-name:hover {
        color: #007bff;
    }
    .slot-category {
        color: #666;
        font-size: 14px;
        background: #f8f9fa;
        padding: 4px 8px;
        border-radius: 12px;
        display: inline-block;
    }
</style>

<div class="background-container">
    <div class="company-header">
        <h2>Slots for {{ $company->company_name }}</h2>
        @if($category)
            <div class="category-filter">
                Category: {{ ucwords(str_replace('_', ' ', $category)) }}
            </div>
        @endif
    </div>
    
    <div class="slot-grid">
        @foreach($slots as $slot)
            @php
                $slotImage = $slot->main_image ?? null;
            @endphp
            <div class="slot-card">
                @if($slotImage)
                    <img src="{{ asset($slotImage) }}" 
                         alt="{{ $slot->name }}" 
                         class="slot-image">
                @endif
                <div class="slot-content">
                    <a href="{{ route('inventory.slot', $slot->id) }}" class="slot-name">
                        {{ $slot->name }}
                    </a>
                    <div class="slot-category">
                        {{ ucwords(str_replace('_', ' ', $slot->category)) }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection 