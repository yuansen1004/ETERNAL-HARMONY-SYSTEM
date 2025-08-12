@extends('layout')

@section('content')
<style>    
    .compare-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .back-btn {
        background: #f3f4f6;
        color: #848b96;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    .back-btn:hover {
        background-color: #a5a6a8;
        color: #191c21;
    }
    
    .compare-message {
        text-align: center;
        padding: 40px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 30px;
    }
    
    .add-more-btn {
        display: inline-block;
        padding: 12px 24px;
        background: #f3f4f6;
        color: #848b96;
        border-radius: 6px;
        text-decoration: none;
        margin-top: 20px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    .add-more-btn:hover {
        background-color: #a5a6a8;
        color: #191c21;
    }
    
    .selected-package {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin: 20px auto;
        max-width: 400px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .remove-btn {
        background: #ef4444;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        margin-top: 10px;
        transition: background-color 0.3s ease;
    }
    
    .remove-btn:hover {
        background-color: #dc2626;
    }
    
    .comparison-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .comparison-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .comparison-title {
        font-size: 20px;
        margin-bottom: 15px;
        color: #333;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .comparison-feature {
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
    }
    
    .feature-name {
        font-weight: 600;
        color: #555;
    }
    
    .feature-value {
        color: #333;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }
    
    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }
    
    .alert-danger {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    
    .btn-primary {
        background-color: #f3f4f6;
        color: #848b96;
        padding: 12px 24px;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease, transform 0.2s ease;
        width: 100%;
        margin-top: 15px;
    }
    
    .btn-primary:hover {
        background-color: #a5a6a8;
        color: #191c21;
        transform: translateY(-1px);
    }
    
    .single-package-container {
        max-width: 500px;
        margin: 0 auto;
    }
    
    .package-actions {
        margin-top: 20px;
        display: flex;
        gap: 10px;
        flex-direction: column;
    }
</style>

<div class="background-container">
    <div class="compare-header">
        <h2>Compare Packages</h2>
        <div style="display: flex; gap: 10px;">
            @if(count($packages) > 0)
                <a href="{{ route('packages.compare.clear') }}" class="back-btn" style="background: #ef4444; color: white;">
                    Clear All
                </a>
            @endif
        <a href="{{ route('packages.browse') }}" class="back-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
            </svg>
            Back to Browse
        </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(count($packages) == 1)
        <div class="single-package-container">
        <div class="compare-message">
                <h3>Selected Package</h3>
                <p>You can purchase this package or add another one to compare</p>
            </div>
            
            <div class="selected-package">
                <h4>{{ $packages[0]->package_name }}</h4>
                <p><strong>Price:</strong> RM{{ number_format($packages[0]->price, 2) }}</p>
                <p><strong>Company:</strong> {{ $packages[0]->company->company_name }}</p>
                <p><strong>Description:</strong> {!! $packages[0]->description !!}</p>
                
                <div class="package-actions">
                    <form action="{{ route('customer.details') }}" method="GET">
                        <input type="hidden" name="package_id" value="{{ $packages[0]->id }}">
                        <button type="submit" class="btn-primary">Purchase Package</button>
                    </form>
                
                <form action="{{ route('packages.compare.remove', $packages[0]->id) }}" method="GET">
                    <button type="submit" class="remove-btn">Remove Package</button>
                </form>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('packages.browse') }}" class="add-more-btn">
                    Browse More Packages to Compare
            </a>
            </div>
        </div>
    @elseif(count($packages) == 2)
        <div class="comparison-grid">
            @foreach($packages as $package)
                <div class="comparison-card">
                    <h3 class="comparison-title">{{ $package->package_name }}</h3>
                    
                    <div class="comparison-feature">
                        <span class="feature-name">Price:</span>
                        <span class="feature-value">RM{{ number_format($package->price, 2) }}</span>
                    </div>
                    
                    <div class="comparison-feature">
                        <span class="feature-name">Company:</span>
                        <span class="feature-value">{{ $package->company->company_name }}</span>
                    </div>
                    
                    <div class="comparison-feature">
                        <span class="feature-name">Description:</span>
                        <span class="feature-value">{!! $package->description !!}</span>
                    </div>
                    
                    <div class="package-actions">
                    <form action="{{ route('customer.details') }}" method="GET">
                        <input type="hidden" name="package_id" value="{{ $package->id }}">
                            <button type="submit" class="btn-primary">Purchase Package</button>
                        </form>
                        
                        <form action="{{ route('packages.compare.remove', $package->id) }}" method="GET">
                            <button type="submit" class="remove-btn">Remove Package</button>
                    </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="compare-message">
            <h3>Select packages to compare</h3>
            <p>Click on packages in the browse page to add them to comparison</p>
            <a href="{{ route('packages.browse') }}" class="add-more-btn">
                Browse Packages
            </a>
        </div>
    @endif
</div>

<script>
// Clear comparison when user navigates away from comparison page
window.addEventListener('beforeunload', function() {
    // This will show a confirmation dialog if user tries to leave with unsaved changes
    // For now, we'll just let them leave without confirmation
});

// Clear comparison when user clicks on navigation links (except comparison-related ones)
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('a[href*="/dashboard"], a[href*="/inventory"], a[href*="/company"], a[href*="/customers"], a[href*="/order"], a[href*="/events"], a[href*="/admin_staff"], a[href*="/ocr_system"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Clear comparison when navigating to other main sections
            fetch('{{ route("packages.compare.clear") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
        });
    });
});
</script>
@endsection