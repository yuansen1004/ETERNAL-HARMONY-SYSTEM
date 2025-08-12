@extends('layout')

@section('content')
<style>
    .edit-form-container {
        background-color: #ffffff;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 600px;
        margin: 50px auto;
        border: 1px solid #e0e0e0;
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

    .form-group input[type="file"],
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="date"],
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

    .form-group input[type="file"]:focus,
    .form-group input[type="text"]:focus,
    .form-group input[type="email"]:focus,
    .form-group input[type="date"]:focus,
    .form-group textarea:focus {
        border-color: #848b96;
        box-shadow: 0 0 0 3px #f4f7f6;
        outline: none;
    }

    .form-group .error-message {
        color: #e74c3c;
        font-size: 0.85em;
        margin-top: 6px;
        padding-left: 5px;
    }

    .btn-primary {
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
        box-shadow: 0 2px 10px #a5a6a8;
        margin-bottom: 15px;
        text-align: center;
    }

    .btn-primary:hover {
        background-color: #a5a6a8;
        color: #191c21;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px #707070;
    }

    .btn-primary:active {
        transform: translateY(0);
        box-shadow: 0 1px 5px #707070;
    }

    .btn-secondary {
        display: block;
        width: auto;
        padding: 12px;
        background-color: #e0e0e0;
        color: #555;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 18px;
        font-weight: 600;
        transition: background-color 0.3s ease, transform 0.2s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        text-decoration: none;
    }

    .btn-secondary:hover {
        background-color: #d0d0d0;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    }

    .btn-secondary:active {
        transform: translateY(0);
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="edit-form-container">
    <h2>Edit Company</h2>

    <form action="{{ route('company.update', $company->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="company_name">Company Name</label>
            <input type="text" id="company_name" name="company_name" value="{{ $company->company_name }}" required>
        </div>
        <div class="form-group">
            <label for="contact_email">Email</label>
            <input type="email" id="contact_email" name="contact_email" value="{{ $company->contact_email }}" required>
        </div>
        <div class="form-group">
            <label for="phone_number">Phone</label>
            <input type="text" id="phone_number" name="phone_number" value="{{ $company->phone_number }}" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" rows="3" required>{{ $company->address }}</textarea>
        </div>
        <button type="submit" class="btn-primary">Update</button>
        <a href="{{ route('company.list') }}" class="btn-secondary">Cancel</a>
    </form>
</div>
@endsection