@extends('layout')

@section('content')
<link rel="stylesheet" href="{{ asset('css/addForm.css') }}">

<div class="add-form-container">
    <h2>Add Event</h2>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="add-form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="add-form-group">
            <label for="main_image">Main Image</label>
            <input type="file" id="main_image" name="main_image" required>
            @error('main_image')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="add-form-group">
            <label for="more_images">Additional Images</label>
            <input type="file" id="more_images" name="more_images[]" multiple>
            @error('more_images.*')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="add-form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Enter event description..." required>{{ old('description') }}</textarea>
            @error('description')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="add-form-group">
            <label for="sub_description">Sub Description</label>
            <textarea id="sub_description" name="sub_description" placeholder="Enter sub description...">{{ old('sub_description') }}</textarea>
            @error('sub_description')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="add-form-group">
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
            @error('start_date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <div class="add-form-group">
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
            @error('end_date')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="add-form-submit">Submit</button>
    </form>
</div>
@endsection