<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Laravel To-Do') }}</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .auth-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-card {
            background-color: #ffffff;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>

<body>
    <div id="app" class="d-flex flex-column min-vh-100">

        @unless (request()->is('login') || request()->is('register'))
            <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                <div class="container">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'To-Do') }}
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto"></ul>

                        <ul class="navbar-nav ms-auto">
                            @guest
                                <!-- Login/Register Links (if needed) -->
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        {{-- User Logout Link with Icon --}}
                                        <a class="dropdown-item" href="#" id="logout-link" title="Logout">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </a>

                                        {{-- Hidden User Logout Form --}}
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
        @endunless

        @if (request()->is('login') || request()->is('register'))
            <main class="auth-wrapper">
                <div class="auth-card">
                    @yield('content')
                </div>
            </main>
        @else
            <main class="py-4 flex-grow-1">
                @yield('content')
            </main>
        @endif
    </div>

    <!-- SweetAlert Notifications & Logout Confirmation -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('status'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('status') }}',
                    confirmButtonColor: '#3085d6'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#d33'
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonColor: '#d33'
                });
            @endif

            // User Logout with Enhanced Confirmation
            const logoutLink = document.getElementById('logout-link');
            const logoutForm = document.getElementById('logout-form');

            if (logoutLink && logoutForm) {
                logoutLink.addEventListener('click', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Logout Confirmation',
                        text: "Are you sure you want to logout?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i>Yes, Logout',
                        cancelButtonText: '<i class="bi bi-x-circle me-1"></i>Cancel',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-danger',
                            cancelButton: 'btn btn-secondary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            Swal.fire({
                                title: 'Logging out...',
                                text: 'Please wait while we log you out.',
                                icon: 'info',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Submit the logout form
                            logoutForm.submit();
                        }
                    });
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
