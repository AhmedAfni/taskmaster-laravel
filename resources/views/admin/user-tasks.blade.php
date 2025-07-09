@extends('admin.layout')

@section('content')
    <h2 class="mb-4">Tasks Assigned to {{ $user->name }}</h2>

    @if ($tasks->isEmpty())
        <div class="alert alert-info">No tasks assigned to this user.</div>
    @else
        <div class="table-responsive">
            <table id="tasksTable" class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Task</th>
                        <th>Status</th>
                        <th>Assigned At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $index => $task)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $task->name }}</td>
                            <td>
                                @if ($task->completed)
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td>{{ $task->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <!-- View -->
                                <button type="button" class="btn btn-outline-info btn-sm" title="View Details"
                                    data-bs-toggle="modal" data-bs-target="#viewTaskModal{{ $task->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <!-- Complete / Undo -->
                                <form
                                    action="{{ $task->completed ? route('admin.tasks.undo', $task->id) : route('admin.tasks.complete', $task->id) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-outline-{{ $task->completed ? 'warning' : 'success' }} btn-sm"
                                        title="{{ $task->completed ? 'Mark as Incomplete' : 'Mark as Complete' }}">
                                        <i class="bi bi-check-circle{{ $task->completed ? '-fill' : '' }}"></i>
                                    </button>
                                </form>

                                <!-- Edit -->
                                <button type="button" class="btn btn-outline-primary btn-sm" title="Edit Task"
                                    data-bs-toggle="modal" data-bs-target="#editTaskModal{{ $task->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Delete -->
                                <form action="{{ route('admin.tasks.delete', $task->id) }}" method="POST"
                                    class="d-inline delete-task-form">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Task">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>

                                <!-- View Task Modal -->
                                <div class="modal fade" id="viewTaskModal{{ $task->id }}" tabindex="-1"
                                    aria-labelledby="viewTaskLabel{{ $task->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="viewTaskLabel{{ $task->id }}">
                                                    <i class="bi bi-eye me-2"></i>Task Details
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Task Title</label>
                                                    <p class="form-control-plaintext border rounded p-3 bg-light mb-0">
                                                        {{ $task->name }}
                                                    </p>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label fw-bold">Description</label>
                                                    <div class="border rounded p-3 bg-light"
                                                        style="min-height: 100px; white-space: pre-wrap; line-height: 1.5;">
                                                        {{ $task->description ?? 'No description provided' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">
                                                    <i class="bi bi-x-circle me-1"></i>Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editTaskModal{{ $task->id }}" tabindex="-1"
                                    aria-labelledby="editTaskLabel{{ $task->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('admin.tasks.edit', $task->id) }}">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editTaskLabel{{ $task->id }}">
                                                        <i class="bi bi-pencil-square me-2"></i>Edit Task
                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Task Title</label>
                                                        <input type="text" name="name" class="form-control"
                                                            value="{{ $task->name }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Description</label>
                                                        <textarea name="description" class="form-control" rows="4" placeholder="Enter task description...">{{ $task->description }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal">
                                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="bi bi-save me-1"></i>Update Task
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <a href="{{ route('admin.users') }}" class="btn btn-secondary mt-3">Back to Users</a>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        .btn-group .btn {
            margin-right: 2px;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        .modal-header .modal-title i {
            color: #6c757d;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#tasksTable').DataTable({
                pageLength: 10,
                ordering: true,
                order: [
                    [3, 'desc']
                ],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ tasks",
                    info: "Showing _START_ to _END_ of _TOTAL_ tasks",
                    emptyTable: "No tasks found"
                }
            });

            // SweetAlert for delete confirmation
            $('.delete-task-form').on('submit', function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Delete Task?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Show success message from session
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#198754',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            // Show error message from session
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#dc3545'
                });
            @endif
        });
    </script>
@endpush
