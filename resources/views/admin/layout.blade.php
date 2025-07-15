<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>

            <div class="ms-auto d-flex align-items-center">
                @auth('admin')
                    <span class="text-white me-3">{{ Auth::guard('admin')->user()->name }}</span>

                    <!-- Logout trigger -->
                    <a href="#" class="btn btn-outline-light btn-sm" id="admin-logout-link"><i
                            class="bi bi-box-arrow-right"></i></a>

                    <!-- Hidden logout form -->
                    <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Logout confirmation -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const logoutLink = document.getElementById('admin-logout-link');
            const logoutForm = document.getElementById('admin-logout-form');

            if (logoutLink && logoutForm) {
                logoutLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You will be logged out.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, logout'
                    }).then((result) => {
                        if (result.isConfirmed) {
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
