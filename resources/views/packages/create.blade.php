@extends('layout')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/addForm.css') }}">

    <div class="add-form-container">
        <h2>Create Package</h2>

        <form action="{{ route('packages.store') }}" method="POST">
            @csrf
            <div class="add-form-group">
                <label for="package_name">Package Name</label>
                <input type="text" id="package_name" name="package_name" required>
            </div>
            <div class="add-form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Enter package description..." required></textarea>
            </div>
            <div class="add-form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>

            <div class="add-form-group">
                <label for="company_id">Company</label>
                <select id="company_id" name="company_id" required>
                    <option value="">Select a Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="add-form-submit">Create Package</button>
            <a href="{{ route('packages.index') }}" class="add-form-cancel">Cancel</a>
        </form>
    </div>
@endsection
