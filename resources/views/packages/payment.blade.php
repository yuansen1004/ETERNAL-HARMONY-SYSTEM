@extends('layout')

@section('content')
<style>
    .payment-container {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
        max-width: 600px;
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

    .form-check {
        margin-bottom: 10px;
    }

    .form-check-input {
        margin-right: 10px;
    }

    .form-check-label {
        cursor: pointer;
        color: #333;
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
        transition: background-color 0.3s ease, transform 0.2s ease;
        width: 100%;
    }

    .btn-primary:hover {
        background-color: #a5a6a8;
        color: #191c21;
        transform: translateY(-1px);
    }

    .installment-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-top: 10px;
        border-left: 4px solid #a5a6a8;
    }

    .package-summary {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        border: 1px solid #e0e0e0;
    }

    .package-summary h3 {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 20px;
    }

    .package-summary p {
        margin: 5px 0;
        color: #666;
    }
</style>

<div class="payment-container">
    <div class="header-row">
        <h2>Complete Purchase</h2>
    </div>

    <div class="package-summary">
        <h3>{{ $package->package_name }}</h3>
        <p><strong>Price:</strong> RM{{ number_format($package->price, 2) }}</p>
        <p><strong>Company:</strong> {{ $package->company->company_name }}</p>
        <p><strong>Description:</strong> {!! $package->description !!}</p>
    </div>

    <form action="{{ route('customer.details.save') }}" method="POST" id="paymentForm">
        @csrf
        <input type="hidden" name="package_id" value="{{ $package->id }}">

        <div class="form-group">
            <label for="customer_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
        </div>

        <div class="form-group">
            <label for="phone_number" class="form-label">Phone Number</label>
            <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Payment Method</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="full_paid" value="full_paid" required>
                <label class="form-check-label" for="full_paid">
                    Full Payment (RM{{ number_format($package->price, 2) }})
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="payment_method" id="installment" value="installment" required>
                <label class="form-check-label" for="installment">
                    Installment Plan
                </label>
            </div>
        </div>

        <div id="installment_options" class="form-group" style="display: none;">
            <label for="installment_period" class="form-label">Installment Period</label>
            <select class="form-control" id="installment_period" name="installment_period">
                <option value="3">3 Months (5% interest)</option>
                <option value="6">6 Months (8% interest)</option>
                <option value="12">12 Months (12% interest)</option>
            </select>
            <div id="installment_calculation" class="installment-info" style="display: none;"></div>
        </div>

        <input type="hidden" id="package_price_data" data-price="{{ $package->price }}">
        <button type="submit" class="btn-primary">Complete Purchase</button>
    </form>
 </div>

 <script>
    const packagePrice = parseFloat(document.getElementById('package_price_data').getAttribute('data-price'));
    const paymentMethod = document.querySelectorAll('input[name="payment_method"]');
    const installmentOptions = document.getElementById('installment_options');
    const installmentPeriod = document.getElementById('installment_period');
    const installmentCalc = document.getElementById('installment_calculation');
    const form = document.getElementById('paymentForm');

    paymentMethod.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'installment') {
                installmentOptions.style.display = 'block';
                calculateInstallment();
            } else {
                installmentOptions.style.display = 'none';
                installmentCalc.style.display = 'none';
            }
        });
    });

    installmentPeriod.addEventListener('change', calculateInstallment);

    function calculateInstallment() {
        const period = parseInt(installmentPeriod.value);
        let interestRate;
        
        switch(period) {
            case 3: interestRate = 0.05; break;
            case 6: interestRate = 0.08; break;
            case 12: interestRate = 0.12; break;
        }

        const totalAmount = packagePrice * (1 + interestRate);
        const monthlyPayment = totalAmount / period;

        installmentCalc.innerHTML = `
            <p><strong>Total Amount:</strong> RM${totalAmount.toFixed(2)}</p>
            <p><strong>Monthly Payment:</strong> RM${monthlyPayment.toFixed(2)} for ${period} months</p>
            <p><strong>Interest Rate:</strong> ${(interestRate * 100)}%</p>
        `;
        installmentCalc.style.display = 'block';
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
        
        if (!selectedPaymentMethod) {
            e.preventDefault();
            alert('Please select a payment method');
            return;
        }

        if (selectedPaymentMethod.value === 'installment') {
            const installmentPeriod = document.getElementById('installment_period').value;
            if (!installmentPeriod) {
                e.preventDefault();
                alert('Please select an installment period');
                return;
            }
        }

        // Log form data for debugging
        console.log('Form submitted with:', {
            package_id: document.querySelector('input[name="package_id"]').value,
            payment_method: selectedPaymentMethod.value,
            installment_period: document.getElementById('installment_period').value
        });
    });
</script>
@endsection