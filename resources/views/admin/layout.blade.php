<!DOCTYPE html>
<html lang="en">

<head>
    {{-- Meta and Title --}}
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - TaskMaster</title>

    {{-- External CSS Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    @stack('styles')
</head>

<body>
    {{-- Navigation Bar --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-shield-check me-2"></i>Admin Dashboard
            </a>

            <div class="ms-auto d-flex align-items-center">
                @auth('admin')
                    <span class="text-white me-3">
                        <i class="bi bi-person-circle me-1"></i>{{ Auth::guard('admin')->user()->name }}
                    </span>

                    {{-- Logout Button --}}
                    <a href="#" class="btn btn-outline-light btn-sm" id="admin-logout-link" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>

                    {{-- Hidden Logout Form --}}
                    <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Main Content Container --}}
    <div class="container mt-4">
        @yield('content')
    </div>

    {{-- External JavaScript Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Admin Logout Confirmation Script --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const logoutLink = document.getElementById('admin-logout-link');
            const logoutForm = document.getElementById('admin-logout-form');

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
