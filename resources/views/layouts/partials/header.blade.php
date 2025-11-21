<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                @php
                    // Determine which dashboard to link to based on role
                    if (auth()->check()) {
                        if (auth()->user()->hasRole('Super Admin')) {
                            $dashboardRoute = route('admin.dashboard');
                        } elseif (auth()->user()->hasRole('employee')) {
                            $dashboardRoute = route('employee.dashboard');
                        } else {
                            $dashboardRoute = '#'; // fallback
                        }
                    } else {
                        $dashboardRoute = route('login');
                    }
                @endphp

                <div class="navbar-brand-box horizontal-logo">
                    <a href="{{ $dashboardRoute }}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-sm.png') }}" alt="logo" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="logo" height="17">
                        </span>
                    </a>

                    <a href="{{ $dashboardRoute }}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-sm.png') }}" alt="logo" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-light.png') }}" alt="logo" height="17">
                        </span>
                    </a>
                </div>

                <button type="button"
                    class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                <!-- App Search -->
                <form class="app-search d-none d-md-block">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Search..." autocomplete="off" id="search-options">
                        <span class="mdi mdi-magnify search-widget-icon"></span>
                    </div>
                </form>
            </div>

            <div class="d-flex align-items-center">

                <!-- Language -->
                <div class="dropdown ms-1 topbar-head-dropdown header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown">
                        <img src="{{ asset('assets/images/flags/us.svg') }}" alt="Header Language" height="20" class="rounded">
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="#" class="dropdown-item notify-item language">
                            <img src="{{ asset('assets/images/flags/us.svg') }}" class="me-2 rounded" height="18"> English
                        </a>
                        <a href="#" class="dropdown-item notify-item language">
                            <img src="{{ asset('assets/images/flags/spain.svg') }}" class="me-2 rounded" height="18"> Español
                        </a>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="dropdown topbar-head-dropdown ms-1 header-item">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" data-bs-toggle="dropdown">
                        <i class='bx bx-bell fs-22'></i>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">3</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0">
                        <div class="dropdown-head bg-secondary bg-pattern rounded-top">
                            <div class="p-3">
                                <h6 class="m-0 fs-16 fw-semibold text-white">Notifications</h6>
                            </div>
                        </div>
                        <div class="p-3 text-center">
                            <p class="mb-0">No new notifications</p>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user" src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-semibold user-name-text">
                                    {{ Auth::user()->name ?? 'User' }}
                                </span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Welcome!</h6>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
