@vite(['resources/css/app.css', 'resources/js/app.js'])

<x-laravel-ui-adminlte::adminlte-layout>
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">


    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <!-- Main Header -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                                class="fas fa-bars"></i></a>
                    </li>
                </ul>
                <li class="nav-item dropdown">
    <a class="nav-link" data-bs-toggle="dropdown" href="#" title="Notifications">
        <i class="fas fa-bell"></i>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-xxl dropdown-menu-end">
        @forelse(auth()->user()->unreadNotifications as $notification)
            <a href="{{ $notification->data['url'] }}" class="dropdown-item">
                <strong>{{ $notification->data['book_title'] }}</strong><br>
                <p> {{ $notification->data['message'] }} </p>
                <span class="text-muted text-sm d-block">{{ $notification->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <span class="dropdown-item text-muted">No new notifications</span>
        @endforelse
        <div class="dropdown-divider"></div>
        <a href="{{ route('notifications.markAllRead') }}" class="dropdown-item dropdown-footer">Mark all as read</a>
    </div>
</li>

                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            <img src="https://kenyaclc.org/wp-content/uploads/2021/02/Managing-Your-Finances.jpeg"
                                class="user-image img-circle elevation-2" alt="User Image">
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <!-- User image -->
                            <li class="user-header bg-dark">
                                <img src="https://kenyaclc.org/wp-content/uploads/2021/02/Managing-Your-Finances.jpeg"
                                    class="img-circle elevation-2" alt="User Image">
                                <p>
                                    {{ Auth::user()->name }}
                                    <small>Member since {{ Auth::user()->created_at->format('M. Y') }}</small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <a href="{{ route('profile.show') }}" class="btn btn-default btn-flat">Profile</a>
                                <a href="#" class="btn btn-default btn-flat float-right"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Sign out
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <!-- Left side column. contains the logo and sidebar -->
            @include('layouts.sidebar')

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                @yield('content')
            </div>

            <!-- Main Footer -->
            <footer class="main-footer">
                <div class="float-right d-none d-sm-block">
                    <b>Version</b> 1.0.0
                </div>
                <strong>Copyright &copy; 2025 <a href="https://lourdesn.github.io/Portfolio/">Lourdes Wairimu</a>.</strong> All rights
                reserved.
            </footer>
        </div>
        <!-- jQuery (if not using Bootstrap 5) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS (for Bootstrap 5) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@yield('scripts')
    </body>
</x-laravel-ui-adminlte::adminlte-layout>
