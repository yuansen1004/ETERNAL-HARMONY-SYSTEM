@extends('layout')
@section('content')
    <style>
        .event-form-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            border: 1px solid #e0e0e0;
        }
        .event-form-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #191c21;
            font-size: 28px;
            font-weight: 600;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 15px;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #dcdcdc;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #848b96;
            box-shadow: 0 0 0 3px #f4f7f6;
            outline: none;
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
        }
        .btn-submit {
            display: block;
            width: 100%;
            padding: 12px;
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
    </style>
    <div class="event-form-container">
        <h2>Purchase Slot Item</h2>
        @php
            $rowPrices = is_string($slot->row_prices) ? json_decode($slot->row_prices, true) : $slot->row_prices;
            $row = $item->row;
            $slotPrice = isset($rowPrices[$row - 1]) ? $rowPrices[$row - 1] : 0;
        @endphp
        <div class="form-group">
            <label><strong>Slot Price:</strong> RM<span id="slot-price">{{ number_format($slotPrice, 2) }}</span></label>
            <div id="installment-info" style="margin-top:8px; display:none;"></div>
        </div>
        <form method="POST" action="{{ route('inventory.purchase', $item->id) }}">
            @csrf
            <div class="form-group">
                <label for="customer_name">Customer Name</label>
                <input type="text" name="customer_name" id="customer_name" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <textarea name="address" id="address" required></textarea>
            </div>
            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select name="payment_method" id="payment_method" required onchange="toggleInstallment()">
                    <option value="full_paid">Full Payment</option>
                    <option value="installment">Installment</option>
                </select>
            </div>
            <div class="form-group" id="installment_period_group" style="display:none;">
                <label for="installment_period">Installment Period (months)</label>
                <select name="installment_period" id="installment_period" onchange="updateInstallmentInfo()">
                    <option value="3">3</option>
                    <option value="6">6</option>
                    <option value="12">12</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Submit Purchase</button>
        </form>
    </div>

    <script>
    const slotPrice = {{ $slotPrice }};
    const interestRates = {3: 0.05, 6: 0.08, 12: 0.12};
    function toggleInstallment() {
        const method = document.getElementById('payment_method').value;
        document.getElementById('installment_period_group').style.display = method === 'installment' ? 'block' : 'none';
        updateInstallmentInfo();
    }
    function updateInstallmentInfo() {
        const method = document.getElementById('payment_method').value;
        const infoDiv = document.getElementById('installment-info');
        if (method !== 'installment') {
            infoDiv.style.display = 'none';
            infoDiv.innerHTML = '';
            return;
        }
        const period = parseInt(document.getElementById('installment_period').value);
        const interest = interestRates[period] || 0;
        const total = slotPrice * (1 + interest);
        const monthly = total / period;
        infoDiv.style.display = '';
        infoDiv.innerHTML = `<strong>Installment Price:</strong> RM${total.toFixed(2)}<br><strong>Monthly Payment:</strong> RM${monthly.toFixed(2)} <span style='color:#888;'>(Interest: ${(interest*100).toFixed(0)}%)</span>`;
    }
    document.addEventListener('DOMContentLoaded', function() {
        toggleInstallment();
        document.getElementById('payment_method').addEventListener('change', toggleInstallment);
        document.getElementById('installment_period').addEventListener('change', updateInstallmentInfo);
    });
    </script>
@endsection 