@extends('layout')

@section('content')
<style>
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

    .form-group textarea {
        min-height: 120px;
        resize: vertical;
        font-family: inherit;
    }
</style>

<div class="event-form-container">
    <h2>Edit Event</h2>
    <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $event->name) }}" required>
        </div>
        <div class="form-group">
            <label for="main_image">Main Image</label>
            <input type="file" id="main_image" name="main_image">
            @if($event->main_image)
                <div style="margin-top: 10px;">
                    <p style="font-size: 14px; color: #666;">Current image: {{ $event->main_image }}</p>
                </div>
            @endif
        </div>
        <div class="form-group">
            <label for="more_images">Additional Images</label>
            <input type="file" id="more_images" name="more_images[]" multiple>
            @if($event->more_images)
                <div style="margin-top: 10px;">
                    <p style="font-size: 14px; color: #666;">Current additional images: {{ $event->more_images }}</p>
                </div>
            @endif
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Enter event description..." required>{{ old('description', $event->description) }}</textarea>
        </div>
        <div class="form-group">
            <label for="sub_description">Sub Description</label>
            <textarea id="sub_description" name="sub_description" placeholder="Enter sub description...">{{ old('sub_description', $event->sub_description) }}</textarea>
        </div>
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $event->start_date) }}" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $event->end_date) }}" required>
        </div>
        <button type="submit" class="btn-primary">Update Event</button>
    </form>
</div>
@endsection