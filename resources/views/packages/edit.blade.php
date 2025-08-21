@extends('layout')

@section('content')
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/quill-custom.css') }}">
    
    <style>
        /* Styles copied from your event-form-container for consistency */
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

        .form-group input[type="file"],
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="date"],
        .form-group input[type="number"], /* Added for price input */
        .form-group textarea,
        .form-group select /* Added for company dropdown */
         {
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
        .form-group input[type="number"]:focus, /* Added for price input */
        .form-group textarea:focus,
        .form-group select:focus /* Added for company dropdown */
         {
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

        /* Adjusted button styles to match the provided design */
        .btn-submit { /* Renamed from btn-primary to avoid conflict with Bootstrap defaults if they exist */
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
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Adjusted shadow for consistency */
            margin-bottom: 15px;
            text-align: center;
        }

        .btn-submit:hover {
            background-color: #a5a6a8;
            color: #191c21;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15); /* Adjusted shadow for consistency */
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1); /* Adjusted shadow for consistency */
        }

        .btn-cancel { /* Renamed from btn-secondary */
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

        .btn-cancel:hover {
            background-color: #d0d0d0;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .btn-cancel:active {
            transform: translateY(0);
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
        }

        /* Quill editor styling */
        .ql-editor {
            min-height: 200px;
            font-family: inherit;
        }
    </style>

    <div class="event-form-container">
        <h2>Edit Package</h2>

        <form action="{{ route('packages.update', $package->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="package_name">Package Name</label>
                <input type="text" id="package_name" name="package_name" value="{{ old('package_name', $package->package_name) }}" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <div id="quill-editor" style="height: 300px; margin-bottom: 10px; border: 1px solid #ccc; background: #fff;"></div>
                <input type="hidden" id="description" name="description" value="{{ old('description', $package->description) }}" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" step="0.01" value="{{ old('price', $package->price) }}" required>
            </div>

            <div class="form-group">
                <label for="company_id">Company</label>
                {{-- Removed class="form-control" as it's Bootstrap-specific and we're using custom styles --}}
                <select id="company_id" name="company_id" required>
                    <option value="">Select a Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $package->company_id) == $company->id ? 'selected' : '' }}>
                            {{ $company->company_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-submit">Update Package</button>
            {{-- Added a cancel button/link matching the style --}}
            <a href="{{ route('packages.index') }}" class="btn-cancel">Cancel</a>
        </form>
    </div>

    <!-- Quill JavaScript -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="{{ asset('js/quill-editor.js') }}"></script>
@endsection
