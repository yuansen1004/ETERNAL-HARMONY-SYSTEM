@extends('layout')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/addForm.css') }}">
    <div class="add-form-container">
        <h2>Add New Slot</h2>
        
        @if ($errors->any())
            <div class="add-form-error" style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <h4>Please fix the following errors:</h4>
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if (session('error'))
            <div class="add-form-error" style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('inventory.slot.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="add-form-group">
                <label for="name">Slot Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                <span class="add-form-text">The name of this slot (e.g., "Block A", "Section 1")</span>
            </div>
            <div class="add-form-group">
                <label for="starting_slot_number">Starting Slot Number</label>
                <input type="number" name="starting_slot_number" id="starting_slot_number" min="1" value="{{ old('starting_slot_number', 1) }}" required>
                <span class="add-form-text">The first slot will be numbered starting from this number (e.g., if set to 100, slots will be 100, 101, 102, etc.)</span>
            </div>
            <div class="add-form-group">
                <label for="company_id">Company</label>
                <select name="company_id" id="company_id" required>
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="add-form-group">
                <label for="category">Category</label>
                <select name="category" id="category" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $cat)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="add-form-group">
                <label for="columns">Number of Columns (each row will have this many columns)</label>
                <input type="number" name="columns" id="columns" min="1" value="{{ old('columns') }}" required>
            </div>
            <div class="add-form-group">
                <label>Row Groups (Set how many rows at each price)</label>
                <div id="row-groups-section"></div>
                <button type="button" class="add-form-add-row" onclick="addRowGroup()">Add Row Group</button>
                <span class="add-form-text">Example: 3 rows at 3000, 2 rows at 2000 = 5 rows total. All rows in a group have the same price.</span>
            </div>
            <div class="add-form-group">
                <label for="main_image">Main Image (Primary Image)</label>
                <input type="file" name="main_image" id="main_image" accept="image/*">
                <span class="add-form-text">Upload a main/featured image for this slot. This will be the primary image shown for all items in this slot.</span>
            </div>
            <div class="add-form-group">
                <label for="images">Additional Images (Multiple Images)</label>
                <input type="file" name="images[]" id="images" accept="image/*" multiple>
                <span class="add-form-text">Upload multiple additional images for this slot. These will be available as secondary images for all items.</span>
            </div>
            <button type="submit" class="add-form-submit">Add Slot</button>
        </form>
    </div>
    <script>
    function addRowGroup() {
        const section = document.getElementById('row-groups-section');
        const div = document.createElement('div');
        div.className = 'add-form-row-group';
        div.innerHTML = `
            <input type="number" name="row_group_counts[]" placeholder="Number of Rows" min="1" required>
            <input type="number" name="row_group_prices[]" placeholder="Price (RM)" min="0" step="0.01" required>
            <button type="button" class="add-form-remove-row" onclick="this.parentNode.remove()">Remove</button>
        `;
        section.appendChild(div);
        console.log('Row group added');
    }
    
    // Add initial row group when page loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Page loaded, adding initial row group');
        addRowGroup();
    });
    
    // Add form submission debugging
    document.querySelector('form').addEventListener('submit', function(e) {
        console.log('Form submitted');
        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
    });
    </script>
@endsection 