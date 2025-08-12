@extends('layout')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    @php
        // Get today's orders
        $todayOrders = \App\Models\Order::with(['customer', 'package', 'inventoryItem.inventory', 'inventoryItems.inventory'])
            ->whereDate('order_date', today())
            ->orderBy('order_date', 'desc')
            ->get();
    @endphp

    <div class="background-container">
        <div class="header-row">
            <h2>Dashboard</h2>
        </div>
        <div class="d-flex gap-20" style="flex-wrap: wrap; margin-bottom: 32px;">
            <div class="dashboard-card">
                <div class="card-title">Users</div>
                <div class="card-value">{{ \App\Models\User::count() }}</div>
            </div>
            <div class="dashboard-card">
                <div class="card-title">Customers</div>
                <div class="card-value">{{ \App\Models\Customer::count() }}</div>
            </div>
            <div class="dashboard-card">
                <div class="card-title">Orders</div>
                <div class="card-value">{{ \App\Models\Order::count() }}</div>
            </div>
            <div class="dashboard-card">
                <div class="card-title">Packages</div>
                <div class="card-value">{{ \App\Models\Package::count() }}</div>
            </div>
            <div class="dashboard-card">
                <div class="card-title">Companies</div>
                <div class="card-value">{{ \App\Models\Company::count() }}</div>
            </div>
            {{-- Uncomment if you want to show events --}}
            {{-- <div class="dashboard-card">
                <div class="card-title">Events</div>
                <div class="card-value">{{ \App\Models\Event::count() }}</div>
            </div> --}}
        </div>
        
        <div class="mb-20">
            <h3 class="section-title">Today's Orders</h3>
            @if($todayOrders->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Package / Inventory Slot</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Payment Method</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayOrders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>{{ $order->customer->customer_name }}</td>
                                <td>
                                    @if($order->package)
                                        {{ $order->package->package_name }}
                                    @elseif($order->isBulkPurchase())
                                        @foreach($order->getAllInventoryItems() as $item)
                                            Slot: {{ $item->slot_number ?? ($item->row . '-' . $item->column) }}<br>
                                            Inventory: {{ $item->inventory->name ?? 'N/A' }}<br>
                                            @if(!$loop->last)<br>@endif
                                        @endforeach
                                    @elseif($order->inventoryItem)
                                        Slot: {{ $order->inventoryItem->slot_number ?? ($order->inventoryItem->row . '-' . $order->inventoryItem->column) }}<br>
                                        Inventory: {{ $order->inventoryItem->inventory->name ?? 'N/A' }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $order->order_date->format('H:i') }}</td>
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
                                    @if($order->payment_method === 'full_paid')
                                        <span class="badge" style="background-color: #e3f2fd; color: #1976d2; display: inline-block;">
                                            Full Payment
                                        </span><br>
                                        <span class="badge" style="{{ $order->installment_paid ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;' }} display: inline-block; margin-top: 4px;">
                                            {{ $order->installment_paid ? 'Paid' : 'Unpaid' }}
                                        </span>
                                    @elseif($order->payment_method === 'installment')
                                        <span class="badge" style="background-color: #fff3e0; color: #f57c00; display: inline-block;">
                                            Installment ({{ $order->installment_duration }} months)
                                        </span><br>
                                        <span class="badge" style="background-color: #f5f5f5; color: #666; display: inline-block; margin-top: 4px;">
                                            {{ $order->installment_paid }}/{{ $order->installment_duration }} paid
                                        </span>
                                    @else
                                        <span class="badge" style="background-color: #f5f5f5; color: #666; display: inline-block;">
                                            {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->package || $order->inventoryItem)
                                        RM{{ number_format($order->total_amount, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center" style="padding: 40px; color: #666; font-style: italic;">
                    No orders for today
                </div>
            @endif
        </div>
        
        <div class="d-flex gap-15" style="flex-wrap: wrap;">
            <a href="{{ route('orders.list') }}" class="dashboard-link">Order Management</a>
            <a href="{{ route('customers.index') }}" class="dashboard-link">Customer Management</a>
            <a href="{{ route('packages.index') }}" class="dashboard-link">Package Management</a>
            <a href="{{ route('company.list') }}" class="dashboard-link">Company Management</a>
            <a href="{{ route('inventory.index') }}" class="dashboard-link">Inventory Management</a>
            <a href="{{ route('events') }}" class="dashboard-link">Events</a>
        </div>
    </div>
@endsection