@extends('layout')

@section('content')
<style>
    .bulk-purchase-form {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }
    .form-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    .form-section h3 {
        margin-top: 0;
        color: #333;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
    }
    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #555;
    }
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }
    .selected-items {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    .item-card {
        background: white;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .item-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    .item-card h4 {
        margin: 0 0 10px 0;
        color: #333;
        font-size: 16px;
    }
    .original-price {
        color: #666;
        font-size: 14px;
        margin-bottom: 8px;
    }
    .price-input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        background-color: #f8f9fa;
    }
    .price-input:focus {
        outline: none;
        border-color: #28a745;
        box-shadow: 0 0 0 2px rgba(40,167,69,0.25);
        background-color: white;
    }
    .btn-submit {
        background: #28a745;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .btn-submit:hover {
        background: #218838;
    }
    .btn-cancel {
        background: #6c757d;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-right: 10px;
    }
    .btn-cancel:hover {
        background: #5a6268;
    }
    .total-section {
        background: #e8f5e8;
        padding: 15px;
        border-radius: 6px;
        margin-top: 20px;
        border: 1px solid #c3e6c3;
    }
    .total-amount {
        font-size: 18px;
        font-weight: 600;
        color: #155724;
    }
</style>

<div class="bulk-purchase-form">
    <h2>Bulk Purchase - {{ $slot->name }}</h2>
    
    <form method="POST" action="{{ route('inventory.bulk-purchase', $slot->id) }}" id="bulk-purchase-form">
        @csrf
        
        <!-- Customer Information Section -->
        <div class="form-section">
            <h3>Customer Information</h3>
            
            <div class="form-group">
                <label for="customer_name">Customer Name *</label>
                <input type="text" id="customer_name" name="customer_name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="phone_number">Phone Number *</label>
                <input type="text" id="phone_number" name="phone_number" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="address">Address *</label>
                <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
            </div>
        </div>
        
        <!-- Payment Method Section -->
        <div class="form-section">
            <h3>Payment Method</h3>
            
            <div class="form-group">
                <label>
                    <input type="radio" name="payment_method" value="full_paid" checked onchange="toggleInstallmentFields()">
                    Full Payment
                </label>
                <label style="margin-left: 20px;">
                    <input type="radio" name="payment_method" value="installment" onchange="toggleInstallmentFields()">
                    Installment Payment
                </label>
            </div>
            
            <div class="form-group" id="installment-period-group" style="display: none;">
                <label for="installment_period">Installment Period *</label>
                <select id="installment_period" name="installment_period" class="form-control">
                    <option value="3">3 Months (5% interest)</option>
                    <option value="6">6 Months (8% interest)</option>
                    <option value="12">12 Months (12% interest)</option>
                </select>
            </div>
        </div>
        
        <!-- Selected Items Section -->
        <div class="form-section">
            <h3>Selected Items & Custom Pricing</h3>
            <p>Click on each item to edit the custom price. The original price is shown for reference.</p>
            
            <div class="selected-items">
                @foreach($items as $item)
                    @if($item->status === 'available')
                        @php
                            $rowPrices = is_string($slot->row_prices) ? json_decode($slot->row_prices, true) : $slot->row_prices;
                            $originalPrice = isset($rowPrices[$item->row - 1]) ? $rowPrices[$item->row - 1] : 0;
                        @endphp
                        <div class="item-card" data-item-id="{{ $item->id }}">
                            <h4>Position: 
                                @if(isset($item->slot_number))
                                    Slot {{ $item->slot_number }}
                                @else
                                    {{ $item->row }}-{{ $item->column }}
                                @endif
                            </h4>
                            <div class="original-price">
                                <strong>Original Price:</strong> RM{{ number_format($originalPrice, 2) }}
                            </div>
                            <input type="hidden" name="selected_items[]" value="{{ $item->id }}">
                            
                            <label for="price_{{ $item->id }}">Custom Price (RM)</label>
                            <input type="number" 
                                   id="price_{{ $item->id }}" 
                                   name="custom_prices[{{ $item->id }}]" 
                                   class="price-input" 
                                   min="0" 
                                   step="0.01" 
                                   required 
                                   value="{{ $originalPrice }}"
                                   onchange="calculateTotal()"
                                   placeholder="Enter custom price">
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        
        <!-- Total Section -->
        <div class="total-section">
            <div class="total-amount">
                <strong>Order Summary:</strong><br>
                Items: <span id="item-count">{{ count($items) }}</span><br>
                Subtotal: RM <span id="subtotal-amount">0.00</span><br>
                Total Amount: RM <span id="total-amount">0.00</span>
            </div>
            <div id="installment-details" style="display: none; margin-top: 10px;">
                <div>Monthly Payment: RM <span id="monthly-payment">0.00</span></div>
                <div>Total with Interest: RM <span id="total-with-interest">0.00</span></div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div style="margin-top: 20px;">
            <a href="{{ route('inventory.slot', $slot->id) }}" class="btn-cancel">Cancel</a>
            <button type="submit" class="btn-submit">Complete Purchase</button>
        </div>
    </form>
</div>

<script>
    function focusPriceInput(itemId) {
        const input = document.getElementById('price_' + itemId);
        input.focus();
        input.select();
    }
    
    function toggleInstallmentFields() {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const installmentGroup = document.getElementById('installment-period-group');
        const installmentDetails = document.getElementById('installment-details');
        
        if (paymentMethod === 'installment') {
            installmentGroup.style.display = 'block';
            installmentDetails.style.display = 'block';
        } else {
            installmentGroup.style.display = 'none';
            installmentDetails.style.display = 'none';
        }
        
        calculateTotal();
    }
    
    function calculateTotal() {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const priceInputs = document.querySelectorAll('input[name^="custom_prices"]');
        let subtotal = 0;
        
        priceInputs.forEach(input => {
            const price = parseFloat(input.value) || 0;
            subtotal += price;
        });
        
        let total = subtotal;
        let monthlyPayment = 0;
        
        // Update subtotal display
        document.getElementById('subtotal-amount').textContent = subtotal.toFixed(2);
        
        if (paymentMethod === 'installment') {
            const installmentPeriod = parseInt(document.getElementById('installment_period').value);
            const interestRates = {'3': 0.05, '6': 0.08, '12': 0.12};
            const interestRate = interestRates[installmentPeriod] || 0;
            
            total = subtotal * (1 + interestRate);
            monthlyPayment = total / installmentPeriod;
            
            document.getElementById('monthly-payment').textContent = monthlyPayment.toFixed(2);
            document.getElementById('total-with-interest').textContent = total.toFixed(2);
        }
        
        document.getElementById('total-amount').textContent = total.toFixed(2);
    }
    
    // Initialize calculation on page load and set up event listeners
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
        
        // Add click event listeners to item cards
        document.querySelectorAll('.item-card').forEach(function(card) {
            card.addEventListener('click', function() {
                const itemId = this.getAttribute('data-item-id');
                focusPriceInput(itemId);
            });
        });
    });
</script>
@endsection 