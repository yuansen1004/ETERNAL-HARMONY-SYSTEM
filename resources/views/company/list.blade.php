@extends('layout')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/company.list.css') }}">
    <div class="background-container">
        <div class="header-row">
            <h2>Company Management</h2>
            <a href="{{ route('company.create') }}" class="btn-add-company">Add New Company</a>
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
        @if($companies->isEmpty())
            <div class="text-center" style="padding: 20px;">No companies found.</div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Company Name</th>
                        <th>Contact Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($companies as $company)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $company->company_name }}</td>
                        <td>{{ $company->contact_email }}</td>
                        <td>{{ $company->phone_number }}</td>
                        <td>{{ Str::limit($company->address, 50) }}</td>
                        <td>
                            <div class="d-flex gap-10">
                                <a href="{{ route('company.edit', $company->id) }}" class="btn-action btn-edit">Edit</a>
                                <form action="{{ route('company.destroy', $company->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this company?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection