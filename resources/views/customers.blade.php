@extends('layout')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/customers.css') }}">
    <div class="background-container">
        <div class="header-row">
            <h2>Customer Management</h2>
        </div>
        
                <!-- Search and Filter Section -->
        <div class="customer-search-section">
            <form method="GET" action="" class="customer-search-form">
                <!-- Search Bar -->
                <div class="customer-search-group">
                    <label for="search" class="customer-search-label">Search:</label>
                    <input type="text" id="search" name="search" 
                           placeholder="Search by name, email, phone, or address..." 
                           value="{{ request('search') }}" 
                           class="customer-search-input">
                </div>
                
                <!-- Company Filter -->
                <div class="customer-filter-group">
                    <label for="company_id" class="customer-search-label">Filter by Company:</label>
                    <select id="company_id" name="company_id" onchange="this.form.submit()" 
                           class="customer-search-select">
                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                                <!-- Order Type Filter -->
                <div class="customer-filter-group">
                    <label for="order_type" class="customer-search-label">Filter by Order Type:</label>
                    <select id="order_type" name="order_type" onchange="this.form.submit()" 
                           class="customer-search-select">
                        <option value="">All Orders</option>
                        <option value="packages" {{ request('order_type') == 'packages' ? 'selected' : '' }}>Packages Only</option>
                        <option value="slots" {{ request('order_type') == 'slots' ? 'selected' : '' }}>Slots Only</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="customer-action-buttons">
                    <button type="submit" class="search-btn">
                        Search
                    </button>
                    <a href="{{ route('customers.index') }}" class="clear-btn">
                        Clear All
                    </a>
                </div>
            </form>
        </div>
        
        @if($customers->count() > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Packages Purchased</th>
                        <th>Slots Purchased</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $group)
                    <tr>
                        <td>{{ $group['name'] }}</td>
                        <td>{{ $group['email'] }}</td>
                        <td>{{ $group['phone'] }}</td>
                        <td>
                                                         @if($group['package_orders']->count() > 0)
                                 @foreach($group['package_orders'] as $order)
                                     <div class="customer-package-info">
                                         <span class="customer-package-name">{{ $order->package->package_name ?? 'N/A' }}</span>
                                         <span class="customer-company-name">
                                             {{ $order->package->company->company_name ?? 'N/A' }}
                                         </span>
                                         <span class="customer-order-date">
                                             ({{ $order->order_date->format('d M Y') }})
                                         </span>
                                     </div>
                                 @endforeach
                             @else
                                 <span class="customer-no-packages">No packages purchased</span>
                             @endif
                        </td>
                        <td>
                                                         @if($group['slot_orders']->count() > 0)
                                 @foreach($group['slot_orders'] as $order)
                                     <div class="customer-package-info">
                                         @if($order->isBulkPurchase())
                                             <span class="customer-package-name">
                                                 @foreach($order->getAllInventoryItems() as $item)
                                                     Slot: {{ $item->slot_number ?? ($item->row . '-' . $item->column) }}
                                                     @if(!$loop->last), @endif
                                                 @endforeach
                                             </span>
                                         @else
                                             <span class="customer-package-name">
                                                 Slot: {{ $order->inventoryItem->slot_number ?? ($order->inventoryItem->row ?? 'N/A') . '-' . ($order->inventoryItem->column ?? 'N/A') }}
                                             </span>
                                         @endif
                                         <span class="customer-order-date">
                                             ({{ $order->order_date->format('d M Y') }})
                                         </span>
                                     </div>
                                 @endforeach
                             @else
                                 <span class="customer-no-packages">No slots purchased</span>
                             @endif
                        </td>
                        <td>
                            <a href="{{ route('customers.show', $group['id']) }}" class="btn-view-details">View Details</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">
                {{ $customers->links() }}
            </div>
        @else
            <div class="alert alert-info">No customers found</div>
        @endif
    </div>
@endsection