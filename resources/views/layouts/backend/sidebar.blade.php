<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    <img src={{ asset('images/faces/face1.jpg') }} alt="profile">
                    <span class="login-status online"></span>
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ Auth::user()->name }}</span>
                    {{-- ambil role user --}}
                    <span class="text-secondary text-small">{{ Auth::user()->email }}</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('dashboard*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard.index') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('user*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('user.index') }}">
                <span class="menu-title">{{ __('label.user') }}</span>
                <i class="mdi mdi-account menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('categories*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('categories.index') }}">
                <span class="menu-title">{{ __('label.category') }}</span>
                <i class="mdi mdi-palette-advanced menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('product*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('product.index') }}">
                <span class="menu-title">{{ __('label.product') }}</span>
                <i class="mdi mdi-cube menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('transaction*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('transaction.index') }}">
                <span class="menu-title">{{ __('label.transaction') }}</span>
                <i class="mdi mdi-shopping menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('role*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('role.index') }}">
                <span class="menu-title">{{ __('label.role') }}</span>
                <i class="mdi mdi-account-convert menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('icon*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('icon.index') }}">
                <span class="menu-title">Icons</span>
                <i class="mdi mdi-contacts menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ Request::is('chart*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('chart.index') }}">
                <span class="menu-title">Charts</span>
                <i class="mdi mdi-chart-bar menu-icon"></i>
            </a>
        </li>

    </ul>
</nav>
