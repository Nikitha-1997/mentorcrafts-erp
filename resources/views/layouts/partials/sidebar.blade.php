<div class="app-menu navbar-menu">
    <div class="navbar-brand-box">
        <a href="{{ route('dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/MC Logo New Full.png') }}" alt="" height="40">
            </span>
        </a>
        <a href="{{ route('dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="17">
            </span>
        </a>
    </div>

    <div id="scrollbar" data-simplebar class="h-100">
        <div class="container-fluid">
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>Menu</span></li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="ri-dashboard-2-line"></i> <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="ri-group-line"></i> <span>Staff Management</span>
                    </a>
                </li>  
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                        <i class="ri-hand-coin-line"></i> <span>Services</span>
                    </a>
                </li>  
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('leads.index') }}">
                        <i class="ri-user-follow-line"></i> <span>Leads</span>
                    </a>
                </li>  
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                        <i class="ri-team-line"></i> <span>Customers</span>
                    </a>
                </li>  
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                        <i class="ri-tools-line"></i> <span>Settings</span>
                    </a>
                </li> 
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->routeIs('customer-project-details.*') ? 'active' : '' }}" href="{{ route('customer-project-details.index') }}">
                        <i class="ri-article-line"></i> <span>Customer Project Details</span>
                    </a>
                </li> 
<li class="menu-title"><span>Service Clients</span></li>

<li class="nav-item">
    <a class="nav-link menu-link" data-bs-toggle="collapse" href="#serviceClientsMenu" role="button" aria-expanded="false" aria-controls="serviceClientsMenu">
        <i class="ri-hand-heart-line"></i> <span>Services & Clients</span>
    </a>

    <div class="collapse menu-dropdown" id="serviceClientsMenu">
        <ul class="nav nav-sm flex-column">
            @foreach($servicesMenu as $service)
                <li class="nav-item">
                    <a href="{{ route('services.show', $service->id) }}" class="nav-link">
                        <i class="ri-customer-service-2-line"></i> {{ $service->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</li>


            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>
