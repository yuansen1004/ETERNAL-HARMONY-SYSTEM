@extends('layout')

@section('content')
    <style>
        .btn-submit {
            display: inline-block;
            padding: 12px 24px;
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
        .image-slider {
            margin: 20px 0;
            position: relative;
            width: 100%;
        }
        .slider-container {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 100%;
            margin: 0 auto;
        }
        .slider-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 50%;
            font-size: 18px;
            z-index: 2;
        }
        .slider-nav.prev { left: 10px; }
        .slider-nav.next { right: 10px; }
        .slider-images {
            display: flex;
            transition: transform 0.5s ease-in-out;
            width: 100%;
        }
        .slider-image {
            width: 100%;
            height: 340px;
            object-fit: contain;
            flex-shrink: 0;
            flex-basis: 100%;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            background: #f8f9fa;
        }
        .bulk-purchase-controls {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .bulk-purchase-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .bulk-purchase-btn:hover {
            background: #218838;
        }
        .bulk-purchase-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
        }
        .item-checkbox {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 16px;
            height: 16px;
            z-index: 10;
        }
        .seat-cell {
            position: relative;
        }
        .selected-item {
            box-shadow: 0 0 0 2px #28a745;
        }

        /* Temple View Styles */
        .temple-view {
            display: block;
        }

        .temple-slot-container {
            position: relative;
            margin: 20px auto;
            max-width: 1200px;
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .temple-background {
            position: relative;
            width: 100%;
            min-height: calc({{ $slot->rows ?? 5 }} * 60px + 190px); /* Increased height to accommodate roof and slots */
            background: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e0e0e0;
        }

        /* Adjust temple structure to be dynamic */
        .temple-base {
            position: relative;
            width: calc({{ $slot->columns ?? 6 }} * 60px + 60px);
            height: calc({{ $slot->rows ?? 5 }} * 60px + 140px); /* Increased height to accommodate roof and slots */
            background: #ffffff;
            border: 3px solid #000000;
            border-top: none;
            border-radius: 0 0 10px 10px;
            box-shadow: none;
        }

        /* Bottom Layer of the Roof (Longer than temple size) */
        .temple-roof {
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% + 180px); /* Adjust width to be longer */
            height: 80px;
            background: #555; /* Grey color for the base layer */
            border: 3px solid #000000;
            border-bottom: none;
            z-index: 1; /* Set a z-index to manage layers */
            clip-path: polygon(10% 50%, 15% 0, 85% 0, 90% 50%, 100% 100%, 0% 100%);
        }

        /* Middle Layer of the Roof (Same size as the temple) */
        .temple-roof::before {
            content: '';
            position: absolute;
            top: -10px; /* Position slightly above the bottom layer */
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% + 60px); /* Match the temple base width */
            height: 60px;
            background: #333; /* Darker color for middle layer */
            border: 3px solid #000000;
            border-bottom: none;
            z-index: 2;
            clip-path: polygon(15% 50%, 20% 0, 80% 0, 85% 50%, 100% 100%, 0% 100%);
        }
        
        /* Top Layer of the Roof (Smaller than the middle) */
        .temple-roof::after {
            content: '';
            position: absolute;
            top: -20px; /* Position slightly above the middle layer */
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 20px); /* Slightly narrower */
            height: 50px;
            background: #111; /* Even darker color for the top layer */
            border: 3px solid #000000;
            border-bottom: none;
            z-index: 3;
            clip-path: polygon(20% 50%, 25% 0, 75% 0, 80% 50%, 100% 100%, 0% 100%);
        }


        .inventory-overlay {
            position: absolute;
            top: 60px; /* Position below the roof, not centered */
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            /* Removed max-height and overflow-y to eliminate scrollbar */
        }

        .inventory-grid {
            display: grid;
            gap: 4px;
            background: transparent;
            border-radius: 0;
            padding: 20px;
            border: none;
            backdrop-filter: none;
        }

        .grid-slot {
            width: 60px;
            height: 60px;
            background: #ffffff;
            border: 2px solid #000000;
            border-radius: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: #000000;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            position: relative;
            box-shadow: none;
        }

        .grid-slot:hover {
            background: #f0f0f0;
            transform: scale(1.02);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .grid-slot.available {
            background: #e6f7e6;
            border-color: #28a745;
            color: #155724;
        }

        .grid-slot.sold {
            background: #ffeaea;
            border-color: #dc3545;
            color: #721c24;
            cursor: not-allowed;
        }

        .grid-slot.selected {
            box-shadow: 0 0 0 2px #000000;
            background: #e0e0e0;
        }

        .grid-slot .user-name {
            font-size: 8px;
            color: #666666;
            margin-top: 2px;
            max-width: 50px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: center;
        }

        /* Temple View Legend */
        .temple-legend {
            margin-top: 20px;
            font-size: 14px;
            color: #000000;
            text-align: center;
        }

        .temple-legend span {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 6px;
            vertical-align: middle;
            border-radius: 0;
        }

        .temple-legend .available { background: #e6f7e6; border: 2px solid #28a745; }
        .temple-legend .sold { background: #ffeaea; border: 2px solid #dc3545; }

        @media (max-width: 768px) {
            .temple-background {
                min-height: calc({{ $slot->rows ?? 5 }} * 50px + 150px); /* Increased height for mobile */
            }

            .temple-base {
                height: calc({{ $slot->rows ?? 5 }} * 50px + 100px); /* Increased height for mobile */
            }

            .inventory-overlay {
                top: 50px; /* Adjust position for mobile roof */
            }

            .grid-slot {
                width: 50px;
                height: 50px;
                font-size: 10px;
            }
        }
    </style>
    <div class="background-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>{{ $slot->name }} ({{ ucwords(str_replace('_', ' ', $slot->category)) }})</h2>
        </div>
        @php
            $images = [];
            // Get images from the first available item to show as slot images
            $firstItem = $items->first();
            if ($firstItem) {
                if ($firstItem->image) {
                    $images[] = $firstItem->image; // Main image
                }
                if ($firstItem->images) {
                    $additionalImages = json_decode($firstItem->images, true);
                    if (is_array($additionalImages)) {
                        $images = array_merge($images, $additionalImages);
                    }
                }
            }
            $images = array_unique($images);
        @endphp
        @if(!empty($images))
            <div class="image-slider">
                <div class="slider-container">
                    <div class="slider-images" id="slider-images">
                        @foreach($images as $index => $image)
                            <img src="{{ asset($image) }}" alt="Slot Image {{ $index + 1 }}" class="slider-image" data-index="{{ $index }}">
                        @endforeach
                    </div>
                    @if(count($images) > 1)
                        <button class="slider-nav prev" onclick="changeSlide(-1)">&lsaquo;</button>
                        <button class="slider-nav next" onclick="changeSlide(1)">&rsaquo;</button>
                    @endif
                </div>
                @if(count($images) > 1)
                    <div class="slider-dots" id="slider-dots">
                        @foreach($images as $index => $image)
                            <span class="slider-dot {{ $index === 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})"></span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="temple-view" id="temple-view">
            @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'staff', 'agent']))
                <div class="bulk-purchase-controls">
                    <h3>Bulk Purchase (Admin/Staff/Agent Only)</h3>
                    <p>Select multiple available slots to purchase them together with custom pricing.</p>
                    <button id="bulk-purchase-btn" class="bulk-purchase-btn" disabled onclick="proceedToBulkPurchase()">
                        Purchase Selected Slots (0 selected)
                    </button>
                </div>
            @endif

            <div class="temple-slot-container">
                <div class="temple-background">
                    <div class="temple-base">
                        <div class="temple-roof"></div>
                    </div>

                    <div class="inventory-overlay">
                        <div class="inventory-grid" style="grid-template-columns: repeat({{ $slot->columns }}, 60px); grid-template-rows: repeat({{ $slot->rows }}, 60px);">
                            @for($r = 1; $r <= $slot->rows; $r++)
                                @for($c = 1; $c <= $slot->columns; $c++)
                                    @php
                                        $cell = $items->first(fn($i) => $i->row == $r && $i->column == $c);
                                        $status = $cell ? $cell->status : 'available';
                                        $itemId = $cell ? $cell->id : null;
                                        $slotNumber = $cell && isset($cell->slot_number) ? $cell->slot_number : ($r . '-' . $c);
                                    @endphp
                                    @if($itemId && $status === 'available')
                                        @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'staff', 'agent']))
                                            <div class="grid-slot available" data-item-id="{{ $itemId }}" onclick="toggleTempleItemSelection({{ $itemId }})">
                                                {{ $slotNumber }}
                                                @if($cell && $cell->user_name)
                                                    <div class="user-name">{{ $cell->user_name }}</div>
                                                @endif
                                            </div>
                                        @else
                                            <a href="{{ route('inventory.item', $itemId) }}" class="grid-slot available">
                                                {{ $slotNumber }}
                                                @if($cell && $cell->user_name)
                                                    <div class="user-name">{{ $cell->user_name }}</div>
                                                @endif
                                            </a>
                                        @endif
                                    @elseif($itemId)
                                        <a href="{{ route('inventory.item', $itemId) }}" class="grid-slot sold">
                                            {{ $slotNumber }}
                                            @if($cell && $cell->user_name)
                                                <div class="user-name">{{ $cell->user_name }}</div>
                                                @endif
                                        </a>
                                    @else
                                        <span class="grid-slot available">{{ $r }}-{{ $c }}</span>
                                    @endif
                                @endfor
                            @endfor
                        </div>
                    </div>
                </div>

                <div class="temple-legend">
                    <span class="available"></span> Available Slots
                    <span class="sold" style="margin-left:20px;"></span> Sold Slots
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/inventory-slider.js') }}"></script>
    <script>
        let selectedItems = new Set();

        document.addEventListener('DOMContentLoaded', function() {
            var seatMap = document.getElementById('seat-map-{{ $slot->id }}');
            if (seatMap) {
                seatMap.style.gridTemplateColumns = 'repeat({{ $slot->columns }}, 44px)';
            }
            @if(!empty($images))
                window.inventorySlider = new InventorySlider('slider-images', {{ count($images) }});
            @endif
        });

        function toggleItemSelection(itemId) {
            const itemElement = document.querySelector(`div.grid-slot.available[data-item-id="${itemId}"], a.grid-slot.available[href*="${itemId}"]`);
            const checkbox = document.getElementById(`item-${itemId}`);

            if (selectedItems.has(itemId)) {
                selectedItems.delete(itemId);
                itemElement.classList.remove('selected-item');
                if (checkbox) checkbox.checked = false;
            } else {
                selectedItems.add(itemId);
                itemElement.classList.add('selected-item');
                if (checkbox) checkbox.checked = true;
            }

            updateBulkPurchaseButton();
        }

        function updateBulkPurchaseButton() {
            const button = document.getElementById('bulk-purchase-btn');
            const count = selectedItems.size;

            if (count > 0) {
                button.disabled = false;
                button.textContent = `Purchase Selected Slots (${count} selected)`;
            } else {
                button.disabled = true;
                button.textContent = 'Purchase Selected Slots (0 selected)';
            }
        }

        function proceedToBulkPurchase() {
            if (selectedItems.size === 0) {
                alert('Please select at least one item to purchase.');
                return;
            }

            const selectedItemsArray = Array.from(selectedItems);
            const queryString = selectedItemsArray.map(id => `items[]=${id}`).join('&');
            window.location.href = `{{ route('inventory.bulk-purchase.form', $slot->id) }}?${queryString}`;
        }

        // Temple view item selection
        function toggleTempleItemSelection(itemId) {
            const itemElement = document.querySelector(`div.grid-slot[data-item-id="${itemId}"], a.grid-slot[href*="${itemId}"]`);

            if (selectedItems.has(itemId)) {
                selectedItems.delete(itemId);
                itemElement.classList.remove('selected');
            } else {
                selectedItems.add(itemId);
                itemElement.classList.add('selected');
            }

            updateBulkPurchaseButton();
        }
    </script>
@endsection