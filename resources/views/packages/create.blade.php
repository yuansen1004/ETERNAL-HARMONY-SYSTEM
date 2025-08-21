@extends('layout')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/addForm.css') }}">
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quill-custom.css') }}">

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
                <div id="quill-editor" style="height: 300px; margin-bottom: 10px; border: 1px solid #ccc; background: #fff;"></div>
                <input type="hidden" id="description" name="description" required>
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

    <!-- Quill JavaScript -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="{{ asset('js/quill-editor.js') }}"></script>
@endsection
