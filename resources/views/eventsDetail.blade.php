@extends('layout')
<link rel="stylesheet" href="{{ asset('css/eventsDetail.css') }}">
@section('content')
<div class="background-container">
    <div class="row">
        <div class="col-12">
            <h2>{{ $event->name }}</h2>
            <p class="text-muted">{!! $event->sub_description !!}</p>
            <p class="event-dates">
                {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
            </p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 mb-4">
            <img src="{{ asset($event->main_image) }}" alt="{{ $event->name }}" class="img-fluid main-event-image">
        </div>
    </div>

    @if (!empty($event->more_images))
    <div class="row mt-4">
        <div class="col-12">
            <h3 class="mb-3">More Images</h3>
            <div class="event-images-gallery-wrapper">
                <div class="event-images-gallery">
                    @foreach (json_decode($event->more_images, true) as $image)
                        <img src="{{ asset($image) }}" alt="{{ $event->name }}" class="gallery-thumbnail" data-full-image="{{ asset($image) }}">
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Navigation buttons above description -->
    <div class="row event-navigation-description mt-4 mb-3">
        <div class="col-6 text-start">
            @if ($previousEvent)
                <a href="{{ route('events.detail', $previousEvent->id) }}" class="nav-link">
                    &larr; Previous: {{ Str::limit($previousEvent->name, 25) }}
                </a>
            @endif
        </div>
        <div class="col-6 text-end">
            @if ($nextEvent)
                <a href="{{ route('events.detail', $nextEvent->id) }}" class="nav-link">
                    Next: {{ Str::limit($nextEvent->name, 25) }} &rarr;
                </a>
            @endif
        </div>
    </div>

    <!-- Event description without scrollbar -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="event-description">
                {!! $event->description !!}
            </div>
        </div>
    </div>

    <!-- Bottom navigation buttons -->
    <div class="row event-navigation-bottom mt-5 mb-5">
        <div class="col-6 text-start">
            @if ($previousEvent)
                <a href="{{ route('events.detail', $previousEvent->id) }}" class="btn btn-outline-secondary">
                    &larr; Previous: {{ Str::limit($previousEvent->name, 30) }}
                </a>
            @endif
        </div>
        <div class="col-6 text-end">
            @if ($nextEvent)
                <a href="{{ route('events.detail', $nextEvent->id) }}" class="btn btn-outline-secondary">
                    Next: {{ Str::limit($nextEvent->name, 30) }} &rarr;
                </a>
            @endif
        </div>
    </div>

</div>
@endsection
<script src="{{ asset('js/eventsDetail.js') }}"></script>