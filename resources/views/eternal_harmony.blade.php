@extends('customerLayout')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/eternal-harmony.css') }}">

    <div class="eternal-harmony-container">
        @if (session('error'))
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                {{ session('error') }}
            </div>
        @endif
        
        @if ($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <h4>Please fix the following errors:</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if (session('success'))
            <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                {{ session('success') }}
            </div>
        @endif
        
                <div class="search-section">
            <h2 class="search-title">Eternal Harmony</h2>
            <p class="search-subtitle">Find your purchased memorial slots</p>
            
            @if(!isset($company))
                <!-- Step 1: Select Company -->
                <div style="text-align: center; margin-bottom: 30px;">
                    <h3 style="color: #2c3e50; margin-bottom: 15px;">Step 1: Choose a Company</h3>
                    <p style="color: #7f8c8d; margin-bottom: 25px;">Select the company where you purchased your memorial slot</p>
                </div>
                
                <form method="POST" action="{{ route('eternal.harmony.select-company') }}" class="search-form">
                    @csrf
                    <div class="form-group" style="max-width: 400px; margin: 0 auto;">
                        <label for="company_id">Select Company</label>
                        <select name="company_id" id="company_id" required>
                            <option value="">Choose a company...</option>
                            @foreach($companies as $comp)
                                <option value="{{ $comp->id }}">{{ $comp->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div style="text-align: center; margin-top: 20px;">
                        <button type="submit" class="search-btn">Continue</button>
                    </div>
                </form>
            @else
                <!-- Step 2: Search by User Name -->
                <div style="text-align: center; margin-bottom: 30px;">
                    <h3 style="color: #2c3e50; margin-bottom: 15px;">Step 2: Search by User Name</h3>
                    <p style="color: #7f8c8d; margin-bottom: 10px;">Searching in: <strong>{{ $company->company_name }}</strong></p>
                    <a href="{{ route('eternal.harmony') }}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">← Change Company</a>
                </div>
                
                <form method="POST" action="{{ route('eternal.harmony.search') }}" class="search-form">
                    @csrf
                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                    
                    <div class="form-group" style="max-width: 400px; margin: 0 auto;">
                        <label for="user_name">User Name</label>
                        <input type="text" name="user_name" id="user_name" placeholder="Enter user name..." value="{{ isset($validated['user_name']) ? $validated['user_name'] : '' }}" required>
                    </div>
                    
                    <div style="text-align: center; margin-top: 20px;">
                        <button type="submit" class="search-btn">Search Slots</button>
                    </div>
                </form>
            @endif
        </div>
    
    @if(isset($purchasedSlots))
        <div class="results-section">
            <div style="text-align: center; margin-bottom: 20px;">
                <a href="{{ route('eternal.harmony') }}" style="color: #3498db; text-decoration: none; font-size: 0.9em;">← Back to Search</a>
            </div>
            @if(count($purchasedSlots) > 0)
                <div class="search-summary">
                    Found <strong>{{ count($purchasedSlots) }}</strong> slot(s) for 
                    <strong>{{ $validated['user_name'] }}</strong> in 
                    <strong>{{ $company->company_name }}</strong>
                </div>
                
                <div class="slots-grid">
                    @foreach($purchasedSlots as $slot)
                        @if($slot['slot_number'])
                            <div class="slot-card">
                                <div class="slot-number">Slot #{{ $slot['slot_number'] }}</div>
                                <div class="slot-details">
                                    <strong>User:</strong> {{ $slot['user_name'] }}
                                </div>
                            </div>
                        @else
                            <!-- Debug: Show slot data when slot_number is null -->
                            <div class="slot-card" style="background: #ffe6e6;">
                                <div class="slot-number">DEBUG: Slot number is null</div>
                                <div class="slot-details">
                                    <strong>User:</strong> {{ $slot['user_name'] }}
                                </div>
                                <div class="slot-details">
                                    <strong>Raw data:</strong> {{ json_encode($slot) }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="no-results">
                    <h3>No Results Found</h3>
                    <p>No purchased slots found for "{{ $validated['user_name'] }}" in {{ $company->company_name }}.</p>
                    <p>Please check the spelling of the user name or try a different company.</p>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection 