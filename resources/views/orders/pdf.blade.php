<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - {{ $order->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 20px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        .order-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-item {
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        .info-value {
            color: #555;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .customer-details, .order-details, .payment-details {
            background-color: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .item-table th, .item-table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }
        .item-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        .signature-section {
            margin-top: 40px;
            border-top: 2px solid #e9ecef;
            padding-top: 20px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 30px;
            margin-bottom: 5px;
        }
        .signature-label {
            font-size: 14px;
            color: #666;
            text-align: center;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 2px solid #e9ecef;
            padding-top: 20px;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        .important-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .important-note h4 {
            color: #856404;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .important-note p {
            color: #856404;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">ETERNAL HARMONY SYSTEM</div>
        <div class="document-title">ORDER CONFIRMATION & RESPONSIBILITY AGREEMENT</div>
        <div style="color: #7f8c8d;">Generated on: {{ $currentDate }}</div>
    </div>

    <div class="order-info">
        <div class="info-item">
            <span class="info-label">Order ID:</span> {{ $order->id }}
        </div>
        <div class="info-item">
            <span class="info-label">Order Date:</span> {{ $order->order_date->format('d M Y') }}
        </div>
        <div class="info-item">
            <span class="info-label">Order Type:</span> {{ ucfirst($orderType) }}
        </div>
        <div class="info-item">
            <span class="info-label">Responsible Agent:</span> {{ $agentName }}
        </div>
    </div>

    @if($order->user)
        <div class="section">
            <div class="section-title">Agent Information</div>
            <div class="customer-details">
                <div class="info-item">
                    <span class="info-label">Agent Name:</span> {{ $order->user->name }}
                </div>
                <div class="info-item">
                    <span class="info-label">Agent Email:</span> {{ $order->user->email }}
                </div>
                <div class="info-item">
                    <span class="info-label">Agent Role:</span> {{ ucfirst($order->user->role) }}
                </div>
            </div>
        </div>
    @endif

    <div class="section">
        <div class="section-title">Customer Information</div>
        <div class="customer-details">
            <div class="info-item">
                <span class="info-label">Customer Name:</span> {{ $order->customer->customer_name }}
            </div>
            <div class="info-item">
                <span class="info-label">Email:</span> {{ $order->customer->email }}
            </div>
            <div class="info-item">
                <span class="info-label">Phone:</span> {{ $order->customer->phone_number }}
            </div>
            <div class="info-item">
                <span class="info-label">Address:</span> {{ $order->customer->address ?? 'Not provided' }}
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Order Details</div>
        <div class="order-details">
            @if($order->package)
                <div class="info-item">
                    <span class="info-label">Order Type:</span> Package Purchase
                </div>
                <div class="info-item">
                    <span class="info-label">Category:</span> Package Service
                </div>
                <div class="info-item">
                    <span class="info-label">Inventory:</span> {{ $order->package->package_name }}
                </div>
                <div class="info-item">
                    <span class="info-label">Slot:</span> N/A
                </div>
                <div class="info-item">
                    <span class="info-label">Name:</span> {{ $order->customer->customer_name }}
                </div>
                @if($order->package->company)
                    <div class="info-item">
                        <span class="info-label">Company:</span> {{ $order->package->company->company_name }}
                    </div>
                @endif
            @else
                @if($order->isBulkPurchase())
                    <div class="info-item">
                        <span class="info-label">Order Type:</span> Bulk Inventory Purchase
                    </div>
                    <div class="info-item">
                        <span class="info-label">Category:</span>
                        @php
                            $categories = $order->getAllInventoryItems()->pluck('inventory.category')->unique()->filter();
                        @endphp
                        @if($categories->count() > 0)
                            {{ $categories->map(function($cat) { return ucfirst(str_replace('_', ' ', $cat)); })->implode(', ') }}
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="info-item">
                        <span class="info-label">Inventory:</span> Multiple Items
                    </div>
                    <div class="info-item">
                        <span class="info-label">Slot:</span> Multiple Slots
                    </div>
                    <div class="info-item">
                        <span class="info-label">Name:</span> {{ $order->customer->customer_name }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Items Details:</span>
                        <table class="item-table">
                            <thead>
                                <tr>
                                    <th>Slot</th>
                                    <th>Inventory</th>
                                    <th>Category</th>
                                    <th>User Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->getAllInventoryItems() as $item)
                                    <tr>
                                        <td>{{ $item->slot_number ?? ($item->row . '-' . $item->column) }}</td>
                                        <td>{{ $item->inventory->name ?? 'N/A' }}</td>
                                        <td>{{ $item->inventory->category ? ucfirst(str_replace('_', ' ', $item->inventory->category)) : 'N/A' }}</td>
                                        <td>{{ $item->user_name ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="info-item">
                        <span class="info-label">Order Type:</span> Single Inventory Purchase
                    </div>
                    <div class="info-item">
                        <span class="info-label">Category:</span>
                        @if($order->inventoryItem && $order->inventoryItem->inventory)
                            {{ ucfirst(str_replace('_', ' ', $order->inventoryItem->inventory->category)) }}
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="info-item">
                        <span class="info-label">Inventory:</span> {{ $order->inventoryItem->inventory->name ?? 'N/A' }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Slot:</span> {{ $order->inventoryItem->slot_number ?? ($order->inventoryItem->row . '-' . $order->inventoryItem->column) }}
                    </div>
                    <div class="info-item">
                        <span class="info-label">Name:</span> {{ $order->customer->customer_name }}
                    </div>
                    @if($order->inventoryItem->user_name)
                        <div class="info-item">
                            <span class="info-label">User Name:</span> {{ $order->inventoryItem->user_name }}
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Payment Information</div>
        <div class="payment-details">
            <div class="info-item">
                <span class="info-label">Payment Type:</span>
                @if($order->payment_method === 'full_paid')
                    Full Payment
                @else
                    Installment ({{ $order->installment_duration }} months)
                @endif
            </div>
            <div class="info-item">
                <span class="info-label">Total Amount:</span> RM{{ number_format($order->total_amount, 2) }}
            </div>
            @if($order->payment_method === 'installment')
                <div class="info-item">
                    <span class="info-label">Monthly Payment:</span> RM{{ number_format($order->monthly_payment, 2) }}
                </div>

            @endif
            <div class="info-item">
                <span class="info-label">Payment Status:</span> {{ ucfirst($order->payment_status) }}
            </div>

        </div>
    </div>

    <div class="important-note">
        @if($order->user)
            <p><strong>IMPORTANT NOTICE:</strong> By signing below, the agent acknowledges that they are responsible for collecting payment from the customer. If the customer refuses to pay or cancels the order, the agent is financially responsible for the full amount of this order.</p>
        @else
            <p><strong>Order Responsibility:</strong> This order was placed through public access. Please ensure proper verification of customer details and payment collection.</p>
        @endif
    </div>

    <div class="signature-section">
        @if($order->user)
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                <div>
                    <div class="signature-line"></div>
                    <div class="signature-label">Agent Name and Signature</div>
                </div>
                <div>
                    <div class="signature-line"></div>
                    <div class="signature-label">Date</div>
                </div>
            </div>
        @endif
    </div>

    <div class="footer">
        <div style="margin-bottom: 15px;">
            <strong>This document serves as a binding agreement between the agent and Eternal Harmony System.</strong>
        </div>
        <div style="border-top: 1px solid #dee2e6; padding-top: 15px;">
            <strong>Generated on {{ $currentDate }} | Order #{{ $order->id }}</strong>
        </div>
    </div>
</body>
</html>
