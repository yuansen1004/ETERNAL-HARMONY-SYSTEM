@extends('layout')
@section('content')
<style>
    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .header-row h2 {
        margin: 0;
        color: #333;
        font-size: 26px;
        font-weight: 600;
    }
    .event-listing {
        border: 1px solid #e0e0e0;
        border-radius: .25rem;
        text-decoration: none;
        color: inherit;
        padding: 15px;
        display: flex;
        align-items: flex-start;
        transition: all 0.3s ease-in-out;
        margin-bottom: 20px;
        background: #f8f9fa;
    }
    .event-listing:hover {
        border-color: #007bff;
        box-shadow: 0 0.5rem 1rem rgba(0, 123, 255, 0.1);
        transform: translateY(-3px);
    }
    .event-image-container {
        max-width: 300px;
        max-height: 300px;
        width: 100%;
        height: auto;
        overflow: hidden;
        border-radius: .25rem;
        flex-shrink: 0;
        margin-right: 15px;
        padding: 5px;
        background-color: #f8f8f8;
    }
    .event-list-image {
        width: 100%;
        height: auto;
        object-fit: contain;
        display: block;
    }
    .event-details .card-title {
        margin-bottom: 5px;
    }
    .event-details .card-text:last-child {
        margin-bottom: 0;
    }
</style>

<div class="background-container">
    <div class="header-row">
        <h2>Events</h2>
    </div>
    <div class="row">
        @foreach ($events as $event)
        <div class="col-md-12">
            <a href="{{ route('events.detail', $event->id) }}" class="event-listing row align-items-start">
                <div class="col-md-4 event-image-container">
                    <img src="{{ asset($event->main_image) }}" alt="{{ $event->name }}" class="img-fluid event-list-image">
                </div>
                <div class="col-md-8 event-details">
                    <h5 class="card-title">{{ $event->name }}</h5>
                    <p class="card-text text-muted">
                        {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                    </p>
                    <p class="card-text">{!! Str::limit($event->sub_description ?? '', 150) !!}</p>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection