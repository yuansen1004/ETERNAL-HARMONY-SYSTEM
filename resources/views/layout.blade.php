<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/overallpage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/eventsDetail.css') }}">
    <title>Admin Dashboard</title>
    <style>
        .sidebar nav ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        .sidebar nav ul li {
            margin-bottom: 0;
            position: relative;
        }
        .sidebar nav ul li > a,
        .sidebar nav ul li > div > a {
            color: #848b96;
            text-decoration: none;
            padding: 12px 15px 12px 20px;
            display: block;
            border-left: 5px solid transparent;
            transition: background-color 0.3s, color 0.3s, border-left 0.3s;
        }
        .sidebar nav ul li > a.active,
        .sidebar nav ul li.active-parent > div > a,
        .sidebar nav ul li > div > a.active {
            background-color: #e5e7eb;
            color: #191c21;
            border-left: 5px solid #191c21;
        }
        .sidebar nav ul li > a:hover,
        .sidebar nav ul li > div > a:hover {
            background-color: #f0f0f0;
            color: #333;
        }
        .has-submenu {
            cursor: pointer;
            position: relative;
        }
        .has-submenu > a {
            width: 100%;
        }
        /* Remove the arrow */
        /* .has-submenu::after { ... } removed */
        /* Keep active state visually distinct */
        .has-submenu.active,
        .has-submenu:hover {
            background-color: #d1d5db;
        }
        .submenu {
            display: none;
            padding-left: 20px;
            background: #e5e7eb; /* Same as active button color */
        }
        .submenu.show {
            display: block;
        }
        .submenu li a {
            padding: 10px 15px 10px 35px;
            font-size: 14px;
            border-left: 5px solid transparent;
        }
        .submenu li a.active {
            background-color: #9ca3af; /* Even darker for active submenu items */
            color: #191c21;
            border-left: 5px solid #191c21;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <a href="/dashboard"><img src="{{ asset('images/logo.png') }}" alt="Company Logo"></a>
        </div>
        <nav>
            <ul id="sidebar-nav-links">
                <li>
                    <a href="/dashboard" class="{{ Request::is('dashboard') ? 'active' : '' }}">Dashboard</a>
                </li>
                
                {{-- Packages - Different access for staff vs agents --}}
                @if(Auth::check() && Auth::user()->role === 'staff')
                <li class="{{ Request::is('packages*') ? 'active-parent' : '' }}">
                    <div class="has-submenu {{ Request::is('packages*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
                        <a href="/packages" class="{{ Request::is('packages') && !Request::is('packages/*') ? 'active' : '' }}">Packages</a>
                    </div>
                    <ul class="submenu {{ Request::is('packages*') ? 'show' : '' }}">
                        <li><a href="/packages/browse" class="{{ Request::is('packages/browse') ? 'active' : '' }}">View Packages</a></li>
                        <li><a href="/packages" class="{{ Request::is('packages') && !Request::is('packages/*') ? 'active' : '' }}">Packages List</a></li>
                    </ul>
                </li>
                @elseif(Auth::check() && Auth::user()->role === 'agent')
                <li class="{{ Request::is('packages*') ? 'active-parent' : '' }}">
                    <div class="has-submenu {{ Request::is('packages*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
                        <a href="/packages/browse" class="{{ Request::is('packages/browse') ? 'active' : '' }}">Packages</a>
                    </div>
                    <ul class="submenu {{ Request::is('packages*') ? 'show' : '' }}">
                        <li><a href="/packages/browse" class="{{ Request::is('packages/browse') ? 'active' : '' }}">View Packages</a></li>
                    </ul>
                </li>
                @endif
                
                {{-- Inventory - Different access for staff vs agents --}}
                <li class="{{ Request::is('inventory*') ? 'active-parent' : '' }}">
                    <div class="has-submenu {{ Request::is('inventory*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
                        <a href="{{ route('inventory.index') }}" class="{{ Request::is('inventory') && !Request::is('inventory/*') ? 'active' : '' }}">Inventory</a>
                    </div>
                    <ul class="submenu {{ Request::is('inventory*') ? 'show' : '' }}">
                        <li><a href="{{ route('inventory.category', 'columbarium') }}" class="{{ Request::is('inventory/category/columbarium') ? 'active' : '' }}">Columbarium</a></li>
                        <li><a href="{{ route('inventory.category', 'ancestor_pedestal') }}" class="{{ Request::is('inventory/category/ancestor_pedestal') ? 'active' : '' }}">Ancestor Pedestal</a></li>
                        <li><a href="{{ route('inventory.category', 'ancestral_tablet') }}" class="{{ Request::is('inventory/category/ancestral_tablet') ? 'active' : '' }}">Ancestral Tablet</a></li>
                        <li><a href="{{ route('inventory.category', 'burial_plot') }}" class="{{ Request::is('inventory/category/burial_plot') ? 'active' : '' }}">Burial Plot</a></li>
                        {{-- Add Slot - Only for staff, not for agents --}}
                        @if(Auth::check() && Auth::user()->role === 'staff')
                        <li style="margin-top:10px;"><a href="{{ route('inventory.slot.create') }}" class="btn btn-sm btn-success">+ Add Slot</a></li>
                        @endif
                    </ul>
                </li>
                
                {{-- Company - Only for staff, not for agents --}}
                @if(Auth::check() && Auth::user()->role === 'staff')
                <li>
                    <a href="/company" class="{{ Request::is('company') ? 'active' : '' }}">Company</a>
                </li>
                @endif
                
                <li>
                    <a href="/customers" class="{{ Request::is('customers') ? 'active' : '' }}">Customers</a>
                </li>

                <li>
                    <a href="/order" class="{{ Request::is('order') ? 'active' : '' }}">Order</a>
                </li>
                
                {{-- Events - Different access for staff vs agents --}}
                <li class="{{ Request::is('events*') ? 'active-parent' : '' }}">
                    @if(Auth::check() && Auth::user()->role === 'staff')
                        <div class="has-submenu {{ Request::is('events*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
                            <a href="/events" class="{{ Request::is('events') && !Request::is('events/*') ? 'active' : '' }}">Events</a>
                        </div>
                        <ul class="submenu {{ Request::is('events*') ? 'show' : '' }}">
                            <li><a href="/events/view" class="{{ Request::is('events/view') ? 'active' : '' }}">View Event</a></li>
                            <li><a href="/events" class="{{ Request::is('events') && !Request::is('events/*') ? 'active' : '' }}">Event List</a></li>
                            <li><a href="/events/create" class="{{ Request::is('events/create') ? 'active' : '' }}">Add Event</a></li>
                        </ul>
                    @elseif(Auth::check() && Auth::user()->role === 'agent')
                        <div class="has-submenu {{ Request::is('events*') ? 'active' : '' }}" onclick="toggleSubmenu(this)">
                            <a href="/events/view" class="{{ Request::is('events/view') ? 'active' : '' }}">Events</a>
                        </div>
                        <ul class="submenu {{ Request::is('events*') ? 'show' : '' }}">
                            <li><a href="/events/view" class="{{ Request::is('events/view') ? 'active' : '' }}">View Event</a></li>
                        </ul>
                    @else
                        <a href="/events/view" class="{{ Request::is('events*') ? 'active' : '' }}">Events</a>
                    @endif
                </li>
                
                {{-- User Management - Only for staff --}}
                @if(Auth::check() && Auth::user()->role === 'staff')
                <li>
                    <a href="/admin_staff" class="{{ Request::is('admin_staff') || Request::is('admin_staff/*') ? 'active' : '' }}">User Management</a>
                </li>
                @endif
            </ul>
        </nav>
    </div>

    <div class="top-navbar">
        <div class="user-info">
            <div class="user-dropdown-wrapper" id="userDropdownToggle" tabindex="0">
                <span>Welcome, {{ Auth::user()->name ?? 'Guest' }} ({{ ucfirst(Auth::user()->role ?? 'Guest') }})</span>
                <div class="dropdown-menu" id="userDropdownMenu">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dropdown for user menu
            const dropdownToggle = document.getElementById('userDropdownToggle');
            const dropdownMenu = document.getElementById('userDropdownMenu');
            dropdownToggle.addEventListener('click', function() {
                dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
            });
            document.addEventListener('click', function(event) {
                if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.style.display = 'none';
                }
            });
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    dropdownMenu.style.display = 'none';
                }
            });
            dropdownMenu.addEventListener('focusout', function(event) {
                if (!dropdownMenu.contains(event.relatedTarget) && !dropdownToggle.contains(event.relatedTarget)) {
                    dropdownMenu.style.display = 'none';
                }
            });
        });
        // For legacy support
        function toggleSubmenu(element) {
            // No-op, handled below if staff
        }
    </script>
    @if(Auth::check() && (Auth::user()->role === 'staff' || Auth::user()->role === 'agent'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.has-submenu').forEach(function(parent) {
                parent.addEventListener('click', function(e) {
                    // Only toggle if clicking the parent, not the link
                    if (e.target.tagName !== 'A') {
                        const submenu = parent.nextElementSibling;
                        submenu.classList.toggle('show');
                        parent.classList.toggle('active');
                        // Close other submenus
                        document.querySelectorAll('.has-submenu').forEach(function(other) {
                            if (other !== parent) {
                                other.classList.remove('active');
                                if (other.nextElementSibling && other.nextElementSibling.classList.contains('submenu')) {
                                    other.nextElementSibling.classList.remove('show');
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>