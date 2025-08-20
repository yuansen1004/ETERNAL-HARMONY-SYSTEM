@extends('layout')
@section('content')
    <style>
        .company-list {
            margin-bottom: 30px;
        }
        .company-list h4 {
            margin-bottom: 15px;
            color: #333;
            font-size: 20px;
            font-weight: 600;
        }
        .company-list ul {
            list-style: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
        }
        .company-list li a {
            background: #f8fafc;
            color: #333;
            padding: 18px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 17px;
            font-weight: 600;
            transition: box-shadow 0.2s;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
            display: inline-block;
            min-width: 180px;
            flex: 1 1 180px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .company-list li a:hover {
            box-shadow: 0 4px 16px rgba(59,130,246,0.10);
        }
        .slot-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }
        .slot-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            padding: 0;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .slot-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .slot-image-banner {
            width: 100%;
            height: 160px;
            object-fit: cover;
            display: block;
            transition: filter 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }
        .slot-card:hover .slot-image-banner {
            filter: brightness(0.92);
            box-shadow: 0 2px 12px rgba(0,0,0,0.10);
        }
        .slot-content {
            padding: 18px 20px 16px 20px;
        }
        .slot-name {
            font-size: 18px;
            font-weight: 600;
            color: #848b96;
            margin-bottom: 8px;
            text-decoration: none;
            display: block;
            transition: color 0.2s;
        }
        .slot-card:hover .slot-name {
            color: #191c21;
        }
        .slot-company {
            color: #666;
            font-size: 14px;
        }
        .inventory-btn {
            background: #f3f4f6;
            color: #848b96;
            border: none;
            border-radius: 6px;
            padding: 8px 18px;
            font-size: 15px;
            font-weight: 600;
            transition: background 0.2s, color 0.2s;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            cursor: pointer;
        }
        .inventory-btn:hover {
            background: #a5a6a8;
            color: #191c21;
        }
    </style>
    <div class="background-container">
        <div class="header-row" style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
            <h2>{{ ucwords(str_replace('_', ' ', $category)) }} Slots</h2>
            <form method="GET" action="{{ route('inventory.slot.create') }}" style="display: inline;">
                <button type="submit" class="inventory-btn">+ Add Slot</button>
            </form>
        </div>
        <div class="company-list">
            <h4>Filter by Company</h4>
            <ul>
                @foreach($companies as $company)
                    <li>
                        <a href="{{ route('inventory.category', [$category, 'company_id' => $company->id]) }}"
                           style="{{ request('company_id') == $company->id ? 'background:#a5a6a8;color:#191c21;' : '' }}">
                            {{ $company->company_name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="slot-grid">
            @foreach($slots as $slot)
                @php
                    $slotImage = $slot->main_image ?? null;
                @endphp
                <a href="{{ route('inventory.slot', $slot->id) }}" class="slot-card">
                    @if($slotImage)
                        <img src="{{ asset($slotImage) }}" alt="{{ $slot->name }}" class="slot-image-banner">
                    @endif
                    <div class="slot-content">
                        <span class="slot-name">{{ $slot->name }}</span>
                        <div class="slot-company">
                            Company: {{ $companies->firstWhere('id', $slot->company_id)->company_name ?? 'N/A' }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection 