@extends('layout')

@section('content')
<link rel="stylesheet" href="{{ asset('css/addForm.css') }}">

<div class="add-form-container">
    <h2>Add New Company</h2>

    <form action="{{ route('company.store') }}" method="POST">
        @csrf
        <div class="add-form-group">
            <label for="company_name">Company Name</label>
            <input type="text" id="company_name" name="company_name" required>
        </div>
        <div class="add-form-group">
            <label for="contact_email">Email</label>
            <input type="email" id="contact_email" name="contact_email" required>
        </div>
        <div class="add-form-group">
            <label for="phone_number">Phone</label>
            <input type="text" id="phone_number" name="phone_number" required>
        </div>
        <div class="add-form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="3" required></textarea>
        </div>
        <button type="submit" class="add-form-submit">Submit</button>
        <a href="{{ route('company.list') }}" class="add-form-cancel">Cancel</a>
    </form>
</div>
@endsection