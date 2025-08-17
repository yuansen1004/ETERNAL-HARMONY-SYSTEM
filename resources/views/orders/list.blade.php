@extends('layout')

@section('content')
    <style>
        .bg-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>

    <div class="background-container">
        <div class="header-row">
            <h2>Order Management</h2>
        </div>
        <div class="filter-and-add-section">
            <form method="GET" action="" class="filter-form">
                <!-- Search Bar -->
                <div class="filter-group">
                    <label for="search" class="form-label">Search:</label>
                    <input type="text" class="form-select" id="search" name="search" 
                           placeholder="Search by Order ID, Customer, Package, or Inventory..." 
                           value="{{ request('search') }}">
                </div>
                
                <!-- Existing Filters -->
                <div class="filter-group">
                    <label for="package_id" class="form-label">Filter by Package:</label>
                    <select class="form-select" id="package_id" name="package_id" onchange="this.form.submit()">
                        <option value="">All Packages</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>{{ $package->package_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="category" class="form-label">Filter by Inventory Category:</label>
                    <select class="form-select" id="category" name="category" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $cat)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="company_id" class="form-label">Filter by Company:</label>
                    <select class="form-select" id="company_id" name="company_id" onchange="this.form.submit()">
                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="status" class="form-label">Filter by Status:</label>
                    <select class="form-select" id="status" name="status" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="complete" {{ request('status') == 'complete' ? 'selected' : '' }}>Complete</option>
                        <option value="cancel" {{ request('status') == 'cancel' ? 'selected' : '' }}>Cancel</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="d-flex justify-content-end align-items-center gap-10">
                    <button type="submit" class="search-btn">Search</button>
                    <a href="{{ route('orders.list') }}" class="clear-btn">Clear All</a>
                </div>
            </form>
        </div>
    
    <div class="table-responsive">
            <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Package / Inventory Slot</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Payment Progress</th>
                    <th>Total Amount</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
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
                    <td>{{ $order->order_date->format('d M Y') }}</td>
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
                            <div style="text-align: center;">
                                <div style="margin-bottom: 5px;">
                                    <span class="badge" style="background-color: #e3f2fd; color: #1976d2; display: inline-block; font-size: 12px;">
                                        Full Payment
                                    </span>
                                </div>
                                @if($order->payment_progress)
                                    <span class="badge" style="background-color: #d4edda; color: #155724; display: inline-block;">
                                        ✅ Complete
                                    </span>
                                @else
                                    <span class="badge" style="background-color: #f8d7da; color: #721c24; display: inline-block;">
                                        ⏳ Pending
                                    </span>
                                @endif
                            </div>
                        @elseif($order->payment_method === 'installment')
                            <div style="text-align: center;">
                                <div style="margin-bottom: 5px;">
                                    <span class="badge" style="background-color: #fff3e0; color: #f57c00; display: inline-block; font-size: 12px;">
                                        Installment ({{ $order->installment_duration }} months)
                                    </span>
                                </div>
                                <span class="badge" style="background-color: #f5f5f5; color: #666; display: inline-block;">
                                    {{ $order->installment_paid }}/{{ $order->installment_duration }} Paid
                                </span>
                            </div>
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
                    <td style="display: flex; align-items: center; gap: 10px;">
                        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-edit">Edit</a>
                        @if(Auth::user()->role === 'staff' || Auth::user()->role === 'admin')
                            <a href="{{ route('orders.export-pdf', $order->id) }}" class="btn-edit">📄PDF</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
        <div class="custom-pagination" style="display: flex; align-items: center; gap: 8px; margin: 16px 0; justify-content: center;">
            @if ($orders->onFirstPage())
                <span style="font-size:18px; color:#ccc;">&#8592;</span>
            @else
                <a href="{{ $orders->previousPageUrl() }}" style="font-size:18px; color:#333; text-decoration:none;">&#8592;</a>
            @endif

            @for ($i = 1; $i <= $orders->lastPage(); $i++)
                @if ($i == $orders->currentPage())
                    <span style="font-weight:bold; color:#333;">{{ $i }}</span>
                @else
                    <a href="{{ $orders->url($i) }}" style="color:#333; text-decoration:none;">{{ $i }}</a>
                @endif
            @endfor

            @if ($orders->hasMorePages())
                <a href="{{ $orders->nextPageUrl() }}" style="font-size:18px; color:#333; text-decoration:none;">&#8594;</a>
            @else
                <span style="font-size:18px; color:#ccc;">&#8594;</span>
            @endif
        </div>
</div>
@endsection