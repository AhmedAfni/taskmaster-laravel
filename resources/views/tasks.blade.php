<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laravel To-Do List</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap + jQuery + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .completed {
            text-decoration: line-through;
            color: gray;
            background-color: #f0f0f0;
            border-left: 5px solid green;
            padding-left: 10px;
        }

        label.error {
            color: red;
            font-size: 0.9rem;
            margin-top: 4px;
            display: block;
            font-weight: 500;
        }

        input.error {
            border-color: red;
        }
    </style>
</head>

<body class="bg-light">

    <!-- ✅ Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">To-Do List</a>
            <div class="ms-auto d-flex align-items-center">
                @auth
                    <span class="text-white me-3">{{ Auth::user()->name }}</span>
                @endauth
                <form action="{{ route('logout') }}" method="POST" class="d-inline" id="logoutForm">
                    @csrf
                    <button type="button" id="logoutBtn" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <h2 class="mb-4 text-center">To-Do List</h2>

                <!-- Add Task Form -->
                <form id="addTaskForm" class="d-flex mb-4">
                    <input type="text" name="name" id="taskName" class="form-control me-2"
                        placeholder="Enter a task..." required>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                </form>

                <!-- Inside your container before the task list -->
                <div class="row mb-4 text-center">
                    <div class="col-md-4 mb-2">
                        <div class="card border-dark shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Total Tasks</h6>
                                <h4 class="fw-bold text-dark">{{ $totalTasks }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card border-success shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Completed</h6>
                                <h4 class="fw-bold text-success">{{ $completedTasks }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="card border-primary shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Pending</h6>
                                <h4 class="fw-bold text-primary ">{{ $pendingTasks }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task List -->
                <ul class="list-group" id="taskList">
                    @foreach ($tasks as $task)
                        <li class="list-group-item" id="task-{{ $task->id }}">
                            @if (!$task->completed)
                                <p class="mb-2 d-flex justify-content-between align-items-center">
                                    <span class="task-text" data-id="{{ $task->id }}"
                                        data-name="{{ $task->name }}">
                                        {{ $task->name }}
                                    </span>
                                    <button class="btn btn-sm btn-warning edit-btn" data-id="{{ $task->id }}"
                                        data-name="{{ $task->name }}" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                </p>
                            @else
                                <p class="mb-2 completed">{{ $task->name }}</p>
                            @endif

                            <!-- 🕒 Timestamp Info -->
                            <small class="text-muted d-block ms-1">
                                Created: {{ $task->created_at->format('M d, Y h:i A') }}
                                @if ($task->completed_at)
                                    | Completed: {{ $task->completed_at->format('M d, Y h:i A') }}
                                @endif
                            </small>

                            <div class="d-flex mt-2">
                                <button class="btn btn-sm btn-outline-success me-2 complete-btn"
                                    data-id="{{ $task->id }}"
                                    title="{{ $task->completed ? 'Undo' : 'Complete' }}">
                                    <i
                                        class="bi {{ $task->completed ? 'bi-arrow-counterclockwise' : 'bi-check2-circle' }}"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $task->id }}"
                                    title="Delete">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>



            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="modalEditForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-1"></i>Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editTaskId">
                    <div class="mb-3">
                        <label for="editTaskName" class="form-label">Task Name</label>
                        <input type="text" class="form-control" id="editTaskName" name="editTaskName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-save"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Section -->
    <script>
        $(document).ready(function() {
            const token = $('meta[name="csrf-token"]').attr('content');
            const editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));

            // Add Task
            $('#addTaskForm').validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    }
                },
                messages: {
                    name: {
                        required: "Please enter a task name.",
                        minlength: "Task must be at least 3 characters long."
                    }
                },
                submitHandler: function(form) {
                    const name = $('#taskName').val().trim();
                    $.post('tasks', {
                        name,
                        _token: token
                    }, function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Task Added!',
                                text: 'Your task has been added successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        }
                    });
                }
            });

            // Edit Task
            $('#modalEditForm').validate({
                rules: {
                    editTaskName: {
                        required: true,
                        minlength: 3
                    }
                },
                messages: {
                    editTaskName: {
                        required: "Please enter a task name.",
                        minlength: "Task must be at least 3 characters long."
                    }
                },
                submitHandler: function() {
                    const id = $('#editTaskId').val();
                    const name = $('#editTaskName').val().trim();

                    $.post(`/tasks/${id}/edit`, {
                        _token: token,
                        name
                    }, function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Task Updated!',
                                text: 'Your task has been updated.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        }
                    });
                }
            });

            // Complete / Undo Task
            $(document).on('click', '.complete-btn', function() {
                const id = $(this).data('id');
                $.post(`/tasks/${id}/complete`, {
                    _token: token
                }, function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: res.completed ? 'Task Completed!' :
                                'Task Marked Incomplete!',
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    }
                });
            });

            // Delete Task
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This task will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(`/tasks/${id}/delete`, {
                            _token: token
                        }, function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Your task has been removed.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $('#task-' + id).remove();
                            }
                        });
                    }
                });
            });

            // Edit Button Trigger
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                $('#editTaskId').val(id);
                $('#editTaskName').val(name);
                editModal.show();
            });

            // Logout confirmation
            $('#logoutBtn').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Logout?',
                    text: 'Are you sure you want to log out?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, logout'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#logoutForm').submit();
                    }
                });
            });
        });
    </script>

</body>

</html>
