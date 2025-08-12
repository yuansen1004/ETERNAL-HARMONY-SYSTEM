@extends('layout')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/addForm.css') }}">

    <div class="add-form-container">
        <h2>Register New User</h2>

        <form method="POST" action="/register">
            @csrf

            <div class="add-form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
                {{-- @error('name')
                    <div class="add-form-error">{{ name error }}</div>
                @enderror --}}
            </div>

            <div class="add-form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                {{-- @error('email')
                    <div class="add-form-error">{{ email error }}</div>
                @enderror --}}
            </div>

            <div class="add-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                {{-- @error('password')
                    <div class="add-form-error">{{ password error }}</div>
                @enderror --}}
            </div>

            <div class="add-form-group">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>Agent</option>
                </select>
            </div>

            <button type="submit" class="add-form-submit">Register</button>
        </form>
    </div>
@endsection