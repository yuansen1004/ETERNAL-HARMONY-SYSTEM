@extends('layout')

@section('content')
    <style>
        .order-edit-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header-row h2 {
            margin: 0;
            color: #333;
            font-size: 26px;
            font-weight: 600;
        }

        .back-btn {
            background-color: #f3f4f6;
            color: #848b96;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #a5a6a8;
            color: #191c21;
        }

        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
        }

        .order-info h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #a5a6a8;
            box-shadow: 0 0 0 2px rgba(165, 166, 168, 0.2);
        }

        .btn-primary {
            background-color: #f3f4f6;
            color: #848b96;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #a5a6a8;
            color: #191c21;
            transform: translateY(-1px);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
            font-size: 15px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
    </style>

    <div class="order-edit-container">
        <div class="header-row">
            <h2>Edit Order Status</h2>
            <a href="{{ route('orders.list') }}" class="back-btn">
                ← Back to Orders
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="order-info">
            <h3>Order Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Order ID</span>
                    <span class="info-value">#{{ $order->id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Customer</span>
                    <span class="info-value">{{ $order->customer->customer_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Package / Slots</span>
                    <span class="info-value">
                        @if($order->package)
                            {{ $order->package->package_name }}
                        @elseif($order->isBulkPurchase())
                            @foreach($order->getAllInventoryItems() as $item)
                                Slot: {{ $item->row }}-{{ $item->column }}<br>
                                @if(!$loop->last)<br>@endif
                            @endforeach
                        @elseif($order->inventory_item_id)
                            Slot: {{ $order->inventoryItem->row ?? 'N/A' }}-{{ $order->inventoryItem->column ?? 'N/A' }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Order Date</span>
                    <span class="info-value">{{ $order->order_date->format('d M Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Payment Method</span>
                    <span class="info-value">
                        @if($order->payment_method === 'full_paid')
                            Full Payment
                        @elseif($order->payment_method === 'installment')
                            Installment ({{ $order->installment_duration }} months)
                        @else
                            {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Amount</span>
                    <span class="info-value">RM{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('orders.update', $order->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Payment Progress</label>
                <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #e0e0e0;">
                    @if($order->payment_method === 'full_paid')
                        <!-- Full Payment Progress -->
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" 
                                   name="payment_progress" 
                                   id="payment_progress" 
                                   value="1" 
                                   {{ $order->payment_progress ? 'checked' : '' }}
                                   {{ Auth::user()->role === 'agent' && $order->payment_progress ? 'disabled' : '' }}>
                            <label for="payment_progress" style="margin: 0; font-weight: 600;">Payment Complete</label>
                            @if(Auth::user()->role === 'agent' && $order->payment_progress)
                                <span style="color: #dc3545; font-size: 14px; margin-left: 10px; font-weight: 500;">
                                    (Cannot be modified once completed)
                                </span>
                            @endif
                        </div>
                        <small style="color: #666; margin-top: 8px; display: block;">
                            <strong>Payment Type:</strong> Full Payment
                        </small>
                    @elseif($order->payment_method === 'installment')
                        <!-- Installment Payment Progress -->
                        <div style="margin-bottom: 15px;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <span style="font-weight: 600; color: #333;">Installment Progress:</span>
                                <span style="font-size: 18px; font-weight: bold; color: #28a745;">{{ $order->installment_paid }}/{{ $order->installment_duration }} Paid</span>
                            </div>
                            
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                @for($i = 1; $i <= $order->installment_duration; $i++)
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <input type="checkbox" 
                                               name="installment_paid_checkboxes[]" 
                                               value="{{ $i }}" 
                                               id="installment_paid_{{ $i }}" 
                                               {{ $order->installment_paid >= $i ? 'checked' : '' }}
                                               {{ Auth::user()->role === 'agent' && $order->installment_paid >= $i ? 'disabled' : '' }}
                                               onclick="updateInstallmentPaid({{ $order->installment_duration }})">
                                        <label for="installment_paid_{{ $i }}" style="margin: 0; font-size: 14px;">{{ $i }}</label>
                                    </div>
                                @endfor
                            </div>
                            
                            <input type="hidden" name="installment_paid" id="installment_paid_hidden" value="{{ $order->installment_paid }}">
                            
                            @if(Auth::user()->role === 'agent' && $order->installment_paid > 0)
                                <small style="color: #dc3545; margin-top: 8px; display: block;">
                                    <strong>Note:</strong> Agents cannot modify completed installment payments.
                                </small>
                            @endif
                        </div>
                        <small style="color: #666; margin-top: 8px; display: block;">
                            <strong>Payment Type:</strong> Installment ({{ $order->installment_duration }} months)
                        </small>
                    @endif
                    
                    <small style="color: #666; margin-top: 15px; display: block; padding-top: 10px; border-top: 1px solid #e0e0e0;">
                        @if(Auth::user()->role === 'agent')
                            <strong>Note:</strong> Agents cannot modify payment progress once marked as complete.
                        @else
                            <strong>Note:</strong> Staff can modify payment progress at any time.
                        @endif
                    </small>
                </div>
            </div>

            <div class="form-group">
                <label for="package_status" class="form-label">Package Usage Status</label>
                <select name="package_status" id="package_status" class="form-control" required>
                    <option value="pending" {{ $order->package_status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="complete" {{ $order->package_status === 'complete' ? 'selected' : '' }}>Complete</option>
                </select>
            </div>

            @if(!$order->package)
                <div class="form-group">
                    <label class="form-label">Edit User Names</label>
                    @if($order->isBulkPurchase())
                        @foreach($order->getAllInventoryItems() as $item)
                            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; padding: 15px; background-color: #f8f9fa; border-radius: 6px; border: 1px solid #e0e0e0;">
                                <div style="flex: 1; min-width: 150px;">
                                    <div style="font-weight: 600; color: #555; font-size: 14px;">Slot:</div>
                                    <div style="color: #333; font-size: 16px;">
                                        @if(isset($item->slot_number))
                                            {{ $item->slot_number }}
                                        @else
                                            {{ $item->row }}-{{ $item->column }}
                                        @endif
                                    </div>
                                </div>
                                <div style="flex: 2;">
                                    <input type="text" 
                                           name="user_names[{{ $item->id }}]" 
                                           value="{{ $item->user_name ?? '' }}" 
                                           placeholder="Enter user name"
                                           style="width: 100%; max-width: 100%; box-sizing: border-box; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                                           >
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px; padding: 15px; background-color: #f8f9fa; border-radius: 6px; border: 1px solid #e0e0e0;">
                            <div style="flex: 1; min-width: 150px;">
                                <div style="font-weight: 600; color: #555; font-size: 14px;">Slot:</div>
                                <div style="color: #333; font-size: 16px;">
                                    @if(isset($order->inventoryItem->slot_number))
                                        {{ $order->inventoryItem->slot_number }}
                                    @else
                                        {{ $order->inventoryItem->row ?? 'N/A' }}-{{ $order->inventoryItem->column ?? 'N/A' }}
                                    @endif
                                </div>
                            </div>
                            <div style="flex: 2;">
                                <input type="text" 
                                       name="user_names[{{ $order->inventoryItem->id }}]" 
                                       value="{{ $order->inventoryItem->user_name ?? '' }}" 
                                       placeholder="Enter user name"
                                       style="width: 100%; max-width: 100%; box-sizing: border-box; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;"
                                       >
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <button type="submit" class="btn-primary">Update Order Status</button>
        </form>
        
        @if($order->payment_method === 'installment')
        <script>
            function updateInstallmentPaid(max) {
                let count = 0;
                for (let i = 1; i <= max; i++) {
                    if (document.getElementById('installment_paid_' + i).checked) count++;
                }
                document.getElementById('installment_paid_hidden').value = count;
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                updateInstallmentPaid({{ $order->installment_duration }});
            });
        </script>
        @endif
    </div>
@endsection 