<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <title>{{ $subpage ?? 'Dashboard' }} &mdash; {{ $content ?? 'Sistem Kasir' }}</title>
        <link rel="icon" href="https://hrd-forum.com/wp-content/uploads/2016/01/Employee-of-the-Month.jpg">

        <link rel="stylesheet" href="{{ asset('!template-stisla/dist/assets/modules/bootstrap/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('!template-stisla/dist/assets/modules/fontawesome/css/all.min.css') }}">

        <link rel="stylesheet" href="{{ asset('!template-stisla/dist/assets/modules/jqvmap/dist/jqvmap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('!template-stisla/dist/assets/modules/summernote/summernote-bs4.css') }}">

        <link rel="stylesheet" href="{{ asset('!template-stisla/dist/assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('!template-stisla/dist/assets/css/components.css') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>

    <body>
        <div id="app">
            <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
                <nav class="navbar navbar-expand-lg main-navbar">
                    <div class="form-inline mr-auto">
                        <ul class="navbar-nav mr-3">
                            <li><a href="" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
                        </ul>
                    </div>

                    {{-- @include('partials.notifications')
                    @include('partials.messages') --}}

                    <ul class="navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                                <div class="fas fa-user mr-3"></div>
                                <div class="d-sm-none d-lg-inline-block">Hi, {{ Auth::user()->name }}</div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a href="" class="dropdown-item has-icon">
                                    <i class="far fa-user"></i> Profil
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="main-sidebar sidebar-style-2">
                    <aside id="sidebar-wrapper">
                        <div class="sidebar-brand">
                            <a href="">QR-CALL</a>
                        </div>
                        <div class="sidebar-brand sidebar-brand-sm">
                            <a href="">QRC</a>
                        </div>
                        <ul class="sidebar-menu">
                            <li class="menu-header">Dashboard {{ Auth::user()->role }}</li>
                            @if (Auth::user()->isAdmin())
                                <li @class(['active' => request()->routeIs('admin.dashboard')])>
                                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="nav-link">
                                        <i class="fas fa-home"></i> <span>Dashboard</span>
                                    </a>
                                </li>
                            @else
                                <li class="{{ Route::is('karyawan.dashboard') ? 'active' : '' }}">
                                    <a href="{{ route('karyawan.dashboard') }}" wire:navigate class="nav-link">
                                        <i class="fas fa-home"></i> <span>Dashboard</span>
                                    </a>
                                </li>
                            @endif
                            <li class="menu-header">Menu Utama {{ Auth::user()->role }}</li>
                            @if((Auth::user()->isKaryawan()))
                                
                            @else
                                <li @class(['active' => request()->routeIs('admin.dashboard.generate-shift', 'admin.dashboard.generate-qr', 'admin.dashboard.check-qr')])>
                                    <a href="{{ route('admin.dashboard.generate-qr') }}" wire:navigate class="nav-link">
                                        <i class="fas fa-qrcode"></i> <span>Generate QR</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </aside>
                </div>

                <div class="main-content">
                    @if(isset($slot))
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endif
                </div>

                <footer class="main-footer">
                    <div class="footer-left">
                        &copy; {{ date('Y') }} - Sistem Kasir
                    </div>
                </footer>
            </div>
        </div>

        <script src="{{ asset('!template-stisla/dist/assets/modules/jquery.min.js') }}"></script>
        <script src="{{ asset('!template-stisla/dist/assets/modules/popper.js') }}"></script>
        <script src="{{ asset('!template-stisla/dist/assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('!template-stisla/dist/assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
        <script src="{{ asset('!template-stisla/dist/assets/js/stisla.js') }}"></script>
    
        <script src="{{ asset('!template-stisla/dist/assets/js/scripts.js') }}"></script>
        <script src="{{ asset('!template-stisla/dist/assets/js/custom.js') }}"></script>
        
        @livewireScripts
        @stack('scripts')

        <script>
            document.addEventListener('livewire:navigated', () => {
                if (typeof $.ready === 'function') {

                }
            });
        </script>
    </body>
</html>