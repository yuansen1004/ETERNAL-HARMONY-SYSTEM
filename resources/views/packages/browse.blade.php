@extends('layout')

@section('content')
<style>
    .filter-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .filter-group {
        flex: 1;
        min-width: 250px;
    }
    
    .filter-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
    }
    
    .filter-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: white;
    }
    
    .apply-btn {
        background: #f3f4f6;
        color: #848b96;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        align-self: flex-end;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    .apply-btn:hover {
        background-color: #a5a6a8;
        color: #191c21;
    }
    
    .package-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .package-card {
        display: block;
        text-decoration: none;
        color: inherit;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
    }
    
    .package-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .package-card.in-comparison {
        opacity: 0.6;
        cursor: not-allowed;
        position: relative;
    }
    
    .package-card.in-comparison:hover {
        transform: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .in-comparison-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #a5a6a8;
        color: #191c21;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .card-header {
        padding: 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        border-radius: 8px 8px 0 0;
    }
    
    .card-title {
        margin: 0;
        font-size: 18px;
        color: #333;
    }
    
    .card-body {
        padding: 15px;
    }
    
    .card-detail {
        margin-bottom: 10px;
    }
    
    .detail-label {
        font-weight: 600;
        color: #555;
    }
    
    .detail-value {
        color: #333;
    }
    
    .price {
        font-size: 20px;
        font-weight: 700;
        color: #2e7d32;
        margin: 15px 0;
    }
    
    .no-results {
        text-align: center;
        padding: 40px;
        color: #666;
        grid-column: 1 / -1;
    }

    .compare-notice {
        color: #3b82f6;
        font-size: 14px;
        margin-top: 10px;
        font-style: italic;
    }
    
    .compare-notice.in-comparison {
        color: #666;
        font-style: normal;
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
</style>

<div class="background-container">
    <h2>Packages</h2>
    
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
    
    @php
        $compareCount = count(session('compare', []));
    @endphp
    
    @if($compareCount > 0)
        <div class="alert" style="background-color: #e3f2fd; color: #1976d2; border-color: #bbdefb;">
            <strong>Comparison Status:</strong> You have {{ $compareCount }} package{{ $compareCount > 1 ? 's' : '' }} in your comparison list. 
            <a href="{{ route('packages.compare') }}" style="color: #1976d2; text-decoration: underline;">View Comparison</a> | 
            <a href="{{ route('packages.compare.clear') }}" style="color: #d32f2f; text-decoration: underline;">Clear All</a>
        </div>
    @endif
    
    <div class="filter-and-add-section" style="margin-bottom: 25px; justify-content: flex-start;">
        <form method="GET" action="{{ route('packages.browse') }}" class="filter-form">
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
                <label for="price_range" class="form-label">Price Range:</label>
                <select class="form-select" id="price_range" name="price_range" onchange="this.form.submit()">
                    <option value="">All Prices</option>
                    <option value="0-100" {{ request('price_range') == '0-100' ? 'selected' : '' }}>Under RM100</option>
                    <option value="100-500" {{ request('price_range') == '100-500' ? 'selected' : '' }}>RM100 - RM500</option>
                    <option value="500-1000" {{ request('price_range') == '500-1000' ? 'selected' : '' }}>RM500 - RM1000</option>
                    <option value="1000+" {{ request('price_range') == '1000+' ? 'selected' : '' }}>Above RM1000</option>
                </select>
            </div>
            <a href="{{ route('packages.browse') }}" class="btn-filter-clear" style="margin-left: 10px;">Clear Filter</a>
        </form>
    </div>
    
    <div class="package-grid">
    @foreach($packages as $package)
        @php
            $compareIds = session('compare', []);
            $inComparison = in_array($package->id, $compareIds);
        @endphp
        
        <div class="package-card {{ $inComparison ? 'in-comparison' : '' }}" 
             data-package-id="{{ $package->id }}" 
             data-in-comparison="{{ $inComparison ? 'true' : 'false' }}">
            @if($inComparison)
                <div class="in-comparison-badge">In Comparison</div>
            @endif
            
        <div class="card-header">
            <h3 class="card-title">{{ $package->package_name }}</h3>
        </div>
        <div class="card-body">
            <div class="price">RM{{ number_format($package->price, 2) }}</div>
            <div class="card-detail">
                <span class="detail-label">Company:</span>
                <span class="detail-value">{{ $package->company->company_name ?? 'N/A' }}</span>
            </div>
                <div class="compare-notice {{ $inComparison ? 'in-comparison' : '' }}">
                    @if($inComparison)
                        Already in comparison list
                    @else
                        Click to add to comparison
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
    
    @if($packages->isEmpty())
        <div class="no-results">
            <h3>No packages found</h3>
            <p>Try adjusting your filters or browse all packages.</p>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.package-card').forEach(card => {
        card.addEventListener('click', function() {
            const inComparison = this.getAttribute('data-in-comparison') === 'true';
            if (!inComparison) {
                const packageId = this.getAttribute('data-package-id');
                window.location.href = '/packages/compare/add/' + packageId;
            }
        });
    });
});
</script>
@endsection