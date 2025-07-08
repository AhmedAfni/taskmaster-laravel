<!DOCTYPE html>
<html>

<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        label.error {
            color: red;
            font-size: 0.9rem;
            margin-top: 4px;
            display: block;
        }

        input.error {
            border-color: red;
        }
    </style>
</head>

<body class="bg-light">
    <div class="card shadow p-4" style="width: 100%; max-width: 400px;">
        <h3 class="mb-4 text-center">Admin Login</h3>

        <form method="POST" action="{{ route('admin.login') }}" id="adminLoginForm" novalidate>
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" />
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control" />
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-success">Login</button>
                <a href="{{ route('admin.register.form') }}" class="btn btn-link text-center">Don't have an account?
                    Register</a>
            </div>
        </form>
    </div>

    <!-- jQuery and Validation Plugin -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            // jQuery form validation
            $('#adminLoginForm').validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 6
                    }
                },
                messages: {
                    email: {
                        required: "Please enter your email",
                        email: "Enter a valid email address"
                    },
                    password: {
                        required: "Please enter your password",
                        minlength: "Password must be at least 6 characters"
                    }
                },
                errorElement: 'span',
                errorClass: 'text-danger small',
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });

            // SweetAlert2 for Laravel session messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
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
        });
    </script>
</body>

</html>
