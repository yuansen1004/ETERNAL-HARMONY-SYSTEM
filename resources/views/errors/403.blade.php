@extends('layout')

@section('content')
<div class="background-container">
    <div class="header-row">
        <h2>Access Denied</h2>
    </div>
    
    <div style="text-align: center; padding: 50px 20px;">
        <div style="font-size: 72px; color: #dc3545; margin-bottom: 20px;">🚫</div>
        <h1 style="color: #dc3545; margin-bottom: 20px;">403 - Access Forbidden</h1>
        <p style="font-size: 18px; color: #666; margin-bottom: 30px;">
            You do not have permission to access this page.
        </p>
        
        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0; max-width: 500px; margin: 0 auto;">
            <h3 style="color: #333; margin-bottom: 15px;">Why am I seeing this?</h3>
            <ul style="text-align: left; color: #666;">
                <li>You are not logged in to the system</li>
                <li>Your account does not have the required permissions</li>
                <li>You may need to contact an administrator for access</li>
            </ul>
        </div>
        
        <div style="margin-top: 30px;">
            <a href="{{ route('login') }}" class="btn-edit" style="margin-right: 15px;">Login</a>
            <a href="{{ url('/') }}" class="btn-edit">Go Home</a>
        </div>
    </div>
</div>
@endsection
