@extends('layout')
@section('content')
    <style>
        .bg-info {
            background-color: #d1ecf1;
            color: #0c5460;
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

        .customer-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #e0e0e0;
        }

        .customer-info h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

        .purchase-history {
            margin-top: 30px;
        }

        .purchase-history h3 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead tr {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }

        .table th,
        .table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }

        .btn-action {
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            margin-right: 5px;
        }
        .btn-edit {
            background-color: #f3f4f6;
            color: #848b96;
        }
        .btn-edit:hover {
            background-color: #a5a6a8;
            color: #191c21;
            transform: translateY(-2px);
        }
        .btn-action:active {
            transform: translateY(0);
        }

        .table th {
            font-weight: 600;
            color: #555;
            font-size: 15px;
            text-transform: uppercase;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: #fbfbfb;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .bg-success {
            background-color: #d4edda;
            color: #155724;
        }

        .bg-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .bg-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .no-purchases {
            text-align: center;
            padding: 40px;
            color: #666;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
    </style>

    <div class="background-container">
        <div class="header-row">
            <h2>Customer Details</h2>
            <a href="{{ route('customers.index') }}" class="back-btn">
                ← Back to Customers
            </a>
        </div>

        <div class="customer-info">
            <h3>Customer Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Customer ID</span>
                    <span class="info-value">#{{ $customer->id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Full Name</span>
                    <span class="info-value">{{ $customer->customer_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email Address</span>
                    <span class="info-value">{{ $customer->email }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone Number</span>
                    <span class="info-value">{{ $customer->phone_number }}</span>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <span class="info-label">Address</span>
                    <span class="info-value">{{ $customer->address }}</span>
                </div>
            </div>
        </div>

        <div class="purchase-history">
            <h3>Packages Purchased</h3>
            @if($package_orders->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Package</th>
                            <th>Company</th>
                            <th>Order Date</th>
                            <th>Payment Progress</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($package_orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->package->package_name ?? 'N/A' }}</td>
                            <td>{{ $order->package->company->company_name ?? 'N/A' }}</td>
                            <td>{{ $order->order_date->format('d M Y') }}</td>
                            <td>
                                @if($order->payment_method === 'full_paid')
                                    @if($order->payment_progress)
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning">Unpaid</span>
                                    @endif
                                @elseif($order->payment_method === 'installment')
                                    <div>
                                        <div style="font-weight: 500; color: #333;">{{ $order->installment_paid }}/{{ $order->installment_duration }} Paid</div>
                                        <div style="font-size: 12px; color: #666;">
                                            @if($order->installment_paid == $order->installment_duration)
                                                <span style="color: #28a745;">Complete</span>
                                            @else
                                                <span style="color: #ffc107;">In Progress</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="badge" style="background-color: #f5f5f5; color: #666;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($order->payment_method === 'installment')
                                    <div>
                                        <div style="font-weight: 500; color: #333;">RM{{ number_format($order->total_amount, 2) }}</div>
                                        <div style="font-size: 12px; color: #666;">
                                            Monthly: RM{{ number_format($order->monthly_payment, 2) }}
                                        </div>
                                    </div>
                                @else
                                    RM{{ number_format($order->total_amount, 2) }}
                                @endif
                            </td>
                            <td>
                                <span class="badge 
                                    @if($order->package_status === 'complete') bg-success
                                    @elseif($order->package_status === 'cancel') bg-danger
                                    @else bg-warning
                                    @endif">
                                    {{ ucfirst($order->package_status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('orders.edit', $order->id) }}" class="btn-action btn-edit">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-purchases">
                    <h4>No Package Purchases</h4>
                    <p>This customer hasn't purchased any packages yet.</p>
                </div>
            @endif
        </div>

        <div class="purchase-history">
            <h3>Slots Purchased</h3>
            @if($slot_orders->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Slot ID</th>
                            <th>User Name</th>
                            <th>Company</th>
                            <th>Order Date</th>
                            <th>Payment Progress</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slot_orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>
                                @if($order->isBulkPurchase())
                                    @foreach($order->getAllInventoryItems() as $item)
                                        @if(isset($item->slot_number))
                                            Slot: {{ $item->slot_number }}<br>
                                        @else
                                            Slot: {{ $item->row }}-{{ $item->column }}<br>
                                        @endif
                                        @if(!$loop->last)<br>@endif
                                    @endforeach
                                @else
                                    @if(isset($order->inventoryItem->slot_number))
                                        Slot {{ $order->inventoryItem->slot_number }}
                                    @else
                                        {{ $order->inventoryItem->row ?? 'N/A' }}-{{ $order->inventoryItem->column ?? 'N/A' }}
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($order->isBulkPurchase())
                                    @foreach($order->getAllInventoryItems() as $item)
                                        {{ $item->user_name ?? 'Not assigned' }}<br>
                                        @if(!$loop->last)<br>@endif
                                    @endforeach
                                @else
                                    {{ $order->inventoryItem->user_name ?? 'Not assigned' }}
                                @endif
                            </td>
                            <td>
                                @if($order->isBulkPurchase())
                                    @php
                                        $firstItem = $order->getAllInventoryItems()->first();
                                        $company = $firstItem ? $firstItem->inventory->company : null;
                                    @endphp
                                    {{ $company ? $company->company_name : 'N/A' }}
                                @else
                                    {{ $order->inventoryItem->inventory->company->company_name ?? 'N/A' }}
                                @endif
                            </td>
                            <td>{{ $order->order_date->format('d M Y') }}</td>
                            <td>
                                @if($order->payment_method === 'full_paid')
                                    @if($order->payment_progress)
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning">Unpaid</span>
                                    @endif
                                @elseif($order->payment_method === 'installment')
                                    <div>
                                        <div style="font-weight: 500; color: #333;">{{ $order->installment_paid }}/{{ $order->installment_duration }} Paid</div>
                                        <div style="font-size: 12px; color: #666;">
                                            @if($order->installment_paid == $order->installment_duration)
                                                <span style="color: #28a745;">Complete</span>
                                            @else
                                                <span style="color: #ffc107;">In Progress</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="badge" style="background-color: #f5f5f5; color: #666;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($order->payment_method === 'installment')
                                    <div>
                                        <div style="font-weight: 500; color: #333;">RM{{ number_format($order->total_amount, 2) }}</div>
                                        <div style="font-size: 12px; color: #666;">
                                            Monthly: RM{{ number_format($order->monthly_payment, 2) }}
                                        </div>
                                    </div>
                                @else
                                    RM{{ number_format($order->total_amount, 2) }}
                                @endif
                            </td>
                            <td>
                                <span class="badge 
                                    @if($order->package_status === 'complete') bg-success
                                    @elseif($order->package_status === 'pending') bg-warning
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($order->package_status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('orders.edit', $order->id) }}" class="btn-action btn-edit">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-purchases">
                    <h4>No Slot Purchases</h4>
                    <p>This customer hasn't purchased any slots yet.</p>
                </div>
            @endif
        </div>
    </div>
@endsection 