@extends('admin.layout')

@section('content')
    <div class="mb-5">
        <h2 class="fw-semibold mb-1">👋 Welcome, {{ Auth::guard('admin')->user()->name }}</h2>
        <p class="text-muted small">Here's a quick snapshot of the platform.</p>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0">Manage Users</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus"></i> Add New User
        </button>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <a href="{{ route('admin.users') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-scale bg-light">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Total Users</h6>
                        <h3 class="fw-bold text-primary">{{ $userCount }}</h3>
                        <small class="text-muted">Registered users</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-scale bg-light">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Admins</h6>
                    <h3 class="fw-bold text-success">{{ $adminCount }}</h3>
                    <small class="text-muted">Active managers</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-scale bg-light">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Today</h6>
                    <h3 class="fw-bold">{{ now()->format('d M Y') }}</h3>
                    <small class="text-muted">{{ now()->format('h:i A') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Task -->
    <div class="mb-5">
        <h4 class="fw-semibold mb-3">Assign a New Task</h4>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.tasks.assign') }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="user_id" class="form-label small fw-semibold">Select User</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="" disabled selected>Choose a user...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="task_name" class="form-label small fw-semibold">Task Title</label>
                            <input type="text" name="task_name" id="task_name" class="form-control"
                                placeholder="e.g. Prepare Report" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark w-100">
                                <i class="bi bi-send"></i> Assign
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- All Tasks -->
    <div>
        <h4 class="fw-semibold mb-3">All User Tasks</h4>
        <div class="table-responsive">
            <table id="userTasksTable" class="table table-hover align-middle border table-sm">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Task</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            <!-- Index for DataTable -->
                            <td class="text-muted"></td>

                            <!-- Task Name -->
                            <td>
                                <span class="{{ $task->completed ? 'text-muted text-decoration-line-through' : '' }}">
                                    {{ $task->name }}
                                </span>
                            </td>

                            <!-- Task Status -->
                            <td>
                                @if ($task->completed)
                                    <span class="badge bg-success">Done</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>

                            <!-- Assigned User -->
                            <td>
                                @if ($task->user)
                                    {{ $task->user->name }}
                                    <br>
                                    <small class="text-muted">{{ $task->user->email }}</small>
                                @else
                                    <span class="text-danger">[User not found]</span>
                                @endif
                            </td>

                            <!-- Created At -->
                            <td>
                                <small>{{ $task->created_at->format('d M Y, h:i A') }}</small>
                            </td>

                            <!-- Actions -->
                            <td>
                                <div class="d-flex gap-1">
                                    <!-- Complete / Undo -->
                                    <form method="POST"
                                        action="{{ $task->completed ? route('admin.tasks.undo', $task) : route('admin.tasks.complete', $task) }}">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm {{ $task->completed ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                            title="{{ $task->completed ? 'Undo Task' : 'Mark Complete' }}">
                                            <i
                                                class="bi {{ $task->completed ? 'bi-arrow-counterclockwise' : 'bi-check2-circle' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Edit -->
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editTaskModal" data-task-id="{{ $task->id }}"
                                        data-task-name="{{ $task->name }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <!-- Delete -->
                                    <form method="POST" action="{{ route('admin.tasks.delete', $task) }}"
                                        class="delete-task-form">
                                        @csrf
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-task-btn"
                                            title="Delete" data-task-id="{{ $task->id }}">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center text-muted" colspan="6">No tasks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="editTaskForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editTaskName" class="form-label">Task Title</label>
                            <input type="text" name="name" id="editTaskName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.users.store') }}" id="addUserForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="userName" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="userName" required>
                        </div>
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="userEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="userPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="userPassword" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />


    <style>
        .hover-scale:hover {
            transform: scale(1.02);
            transition: 0.2s ease-in-out;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables Scripts -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            const $table = $('#userTasksTable');

            if ($table.find('tbody tr').not(':has(td[colspan])').length > 0) {

                let table = $table.DataTable({
                    pageLength: 4,
                    ordering: true,
                    order: [
                        [4, 'asc']
                    ], // ✅ Changed to ascending
                    language: {
                        search: "Search tasks:",
                        lengthMenu: "Show _MENU_ tasks per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ tasks",
                        emptyTable: "No tasks available"
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [0, 5] // '#' and Actions
                    }]
                });

                // Auto-fill row numbers in '#' column
                table.on('order.dt search.dt draw.dt', function() {
                    table.column(0, {
                            search: 'applied',
                            order: 'applied'
                        }).nodes()
                        .each(function(cell, i) {
                            cell.innerHTML = i + 1;
                        });
                }).draw();
            }
        });
    </script>


    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#user_id').select2({
                placeholder: 'Choose a user...',
                allowClear: true,
                width: '100%'
            });

            // SweetAlert Flash Messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#198754'
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#dc3545'
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonColor: '#dc3545'
                });
            @endif

            // Delete Task Confirmation
            $('.delete-task-btn').on('click', function(e) {
                e.preventDefault();

                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This task will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });


            // Assign Task Form Validation
            $('form[action="{{ route('admin.tasks.assign') }}"]').attr('id', 'assignTaskForm');

            $('#assignTaskForm').validate({
                rules: {
                    user_id: {
                        required: true
                    },
                    task_name: {
                        required: true,
                        minlength: 3
                    }
                },
                messages: {
                    user_id: "Please select a user.",
                    task_name: {
                        required: "Please enter a task title.",
                        minlength: "Task title must be at least 3 characters."
                    }
                },
                errorClass: 'text-danger small',
                errorElement: 'div',
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });

            // Edit Task Modal
            const editTaskModal = document.getElementById('editTaskModal');
            editTaskModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const taskId = button.getAttribute('data-task-id');
                const taskName = button.getAttribute('data-task-name');

                const input = editTaskModal.querySelector('#editTaskName');
                const form = editTaskModal.querySelector('#editTaskForm');

                input.value = taskName;
                form.action = `/admin/tasks/${taskId}/edit`;

                // Apply validation when modal is shown
                $('#editTaskForm').validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 3
                        }
                    },
                    messages: {
                        name: {
                            required: "Task title is required.",
                            minlength: "Task title must be at least 3 characters."
                        }
                    },
                    errorClass: 'text-danger small',
                    errorElement: 'div',
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    }
                });

                // Add User Form Validation
                $('#addUserForm').validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 2
                        },
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
                        name: "Please enter a name",
                        email: {
                            required: "Email is required",
                            email: "Enter a valid email"
                        },
                        password: {
                            required: "Password is required",
                            minlength: "Minimum 6 characters"
                        }
                    },
                    errorClass: 'text-danger small',
                    errorElement: 'div',
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    }
                });

            });
        });
    </script>
@endpush
