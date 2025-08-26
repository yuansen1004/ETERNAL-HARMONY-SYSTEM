@extends('layout')

@section('content')

    <div class="background-container">
        {{-- Header row with title and Add Package button --}}
        <div class="header-row">
            <h2>Package Management</h2>
            <a href="{{ route('packages.create') }}" class="btn-add-company">Add New Package</a>
        </div>

        {{-- Section for Filter only --}}
        <div class="filter-and-add-section">
            {{-- Company Filter Form --}}
            <form action="{{ route('packages.index') }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <label for="company_filter" class="form-label">Filter by Company:</label>
                    <select class="form-select" id="company_filter" name="company_id" onchange="this.form.submit()">
                        <option value="">All Companies</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ (string)$company->id === request('company_id') ? 'selected' : '' }}>
                                {{ $company->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <a href="{{ route('packages.index') }}" class="btn-filter-clear">Clear Filter</a>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Display error message if present (from try-catch in controller) --}}
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($packages->isEmpty())
            <div style="text-align: center; padding: 20px;">No packages found.</div>
        @else
            <table class="table" id="packages-table">
                <thead>
                    <tr>
                        <th>Package Name</th>
                        <th>Company</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packages as $package)
                    <tr>
                        <td>{{ $package->package_name }}</td>
                        <td>{{ $package->company->company_name ?? 'N/A' }}</td>
                        <td>{{ $package->price }}</td>
                        <td>{!! $package->description !!}</td>
                        <td>
                            <div style="display: flex;">
                                <a href="{{ route('packages.edit', $package->id) }}" class="btn-action btn-edit">Edit</a>
                                <form action="{{ route('packages.destroy', $package->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this package?')">Delete</button>
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

@section('scripts')
{{-- Ensure you have jQuery and DataTables CSS/JS linked in your layout.blade.blade.php --}}
<script>
    $(document).ready(function() {
        $('#packages-table').DataTable({
            "paging": false,
            "searching": false,
            "info": false
        });
    });
</script>
@endsection 