@extends('layout')

@section('content')
    <div class="background-container">
        <div class="header-row">
            <h2>Event Management</h2>
            @if(auth()->check() && auth()->user()->role === 'staff')
                <a href="{{ route('events.create') }}" class="btn-register-event">Register New Event</a>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($events->isEmpty())
            <div class="text-center" style="padding: 20px;">No events found.</div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Name</th>
                        <th>Main Image</th>
                        <th>Sub Description</th>
                        @if(auth()->check() && auth()->user()->role === 'staff')
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                @forelse($events as $event)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $event->name }}</td>
                        <td><img src="{{ asset($event->main_image) }}" alt="{{ $event->name }}" width="100"></td>
                        <td>{!! Str::limit($event->sub_description, 80) !!}</td>
                        @if(auth()->check() && auth()->user()->role === 'staff')
                        <td>
                            <div class="d-flex gap-10">
                                <a href="{{ route('events.edit', $event->id) }}" class="btn-register-event">Edit</a>
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this event?')">Delete</button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ (auth()->check() && auth()->user()->role === 'staff') ? '5' : '4' }}" class="text-center" style="padding: 20px;">No events found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        @endif
    </div>
@endsection