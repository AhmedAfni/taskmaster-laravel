@extends('admin.layout')
{{-- Extend the admin layout from resources/views/admin/layout.blade.php--}}

@section('content') {{-- Main content section for displaying users --}}
    <h2 class="mb-4">Registered Users</h2> {{-- Page title for users section --}}
    <div class="table-responsive">
        <table id="usersTable" class="table table-bordered table-striped align-middle"> {{--bootstrap styled table--}}
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Loop through users and display each user in a table row --}}
                @forelse($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td> {{-- Display user index --}}
                        <td>{{ $user->name }}</td> {{-- Display user name --}}
                        <td>{{ $user->email }}</td> {{-- Display user email --}}
                        <td>{{ $user->created_at->format('d M Y, h:i A') }}</td> {{-- Display user registration date --}}

                        <td class="d-flex gap-2">
                            {{-- View Tasks Button --}}
                            <a href="{{ route('admin.users.tasks', $user->id) }}"
                                class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center p-1"
                                style="width: 32px; height: 32px;" title="View Tasks">
                                <i class="bi bi-eye" style="font-size: 1rem;"></i>
                            </a>

                            {{-- Delete Button --}}
                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                class="delete-user-form" data-username="{{ $user->name }}"
                                data-task-count="{{ $user->tasks_count }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center p-1"
                                    style="width: 32px; height: 32px;" title="Delete User">
                                    <i class="bi bi-trash" style="font-size: 1rem;"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
@endsection

@push('styles')
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable
            $('#usersTable').DataTable({
                pageLength: 10,
                ordering: true,
                order: [
                    [0, 'asc']
                ],
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ users",
                    emptyTable: "No users available"
                },
                columnDefs: [{
                        orderable: false,
                        targets: [4]
                    } // Disable sorting on 'Actions'
                ]
            });

            // Delete User with check for assigned tasks
            const deleteForms = document.querySelectorAll('.delete-user-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const username = form.dataset.username;
                    const taskCount = parseInt(form.dataset.taskCount);

                    if (taskCount > 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Cannot Delete User',
                            html: `<strong>${username}</strong> has <strong>${taskCount}</strong> assigned task(s).<br>
                                   Please reassign or delete the tasks first.`,
                            confirmButtonColor: '#d33'
                        });
                        return;
                    }

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `This will permanently delete user "${username}".`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

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
                    title: 'Error',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#d33'
                });
            @endif
        });
    </script>
@endpush
