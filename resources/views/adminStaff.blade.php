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

        .btn-register-user {
            background-color: #f3f4f6;
            color: #848b96;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-register-user:hover {
            background-color:#a5a6a8;
            color: #191c21;
            transform: translateY(-1px);
        }

        .btn-register-user:active {
            transform: translateY(0);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead tr {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }

        .table th,
        .table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }

        .table th {
            font-weight: 600;
            color: #555;
            font-size: 15px;
            text-transform: uppercase;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: #fbfbfb;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
            font-size: 15px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .btn-danger {
            background-color: #dc3545;
            color: #fff;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }

        .btn-danger:active {
            transform: translateY(0);
        }
    </style>

    <div class="background-container">
        <div class="header-row">
            <h2>User Management</h2>
            @if(auth()->check() && auth()->user()->role === 'staff')
                <a href="{{ route('register') }}" class="btn-register-user">Register New User</a>
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

        <table class="table">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td>
                        @if(auth()->user()->id !== $user->id)
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection