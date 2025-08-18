{{--
    Admin Dashboard - Main administrative interface for TaskMaster
    Features: User management, task assignment, task monitoring, statistics
--}}
@extends('admin.layout')

@section('content')
    {{-- Welcome Header Section --}}
    <div class="mb-5">
        <h2 class="fw-semibold mb-1">👋 Welcome, {{ Auth::guard('admin')->user()->name }}</h2>
        <p class="text-muted small">Here's a quick snapshot of the platform.</p>
    </div>

    {{-- User Management Section Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0">Manage Users</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus"></i> Add New User
        </button>
    </div>

    {{-- Add Product to User Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0">Add Product to User</h4>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductToUserModal">
            <i class="bi bi-box-seam"></i> Add Product to User
        </button>
    </div>

    {{-- Statistics Cards Row --}}
    <div class="row g-4 mb-5">
        {{-- Total Users Card --}}
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
        {{-- Total Admins Card --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 hover-scale bg-light">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Admins</h6>
                    <h3 class="fw-bold text-success">{{ $adminCount }}</h3>
                    <small class="text-muted">Active managers</small>
                </div>
            </div>
        </div>
        {{-- Current Date/Time Card --}}
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

    {{-- Task Assignment Section --}}
    <div class="mb-5">
        <h4 class="fw-semibold mb-3">Assign a New Task</h4>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                {{-- Task Assignment Form with TinyMCE and CKEditor5 integration --}}
                <form method="POST" action="{{ route('admin.tasks.assign') }}">
                    @csrf
                    <div class="row g-3">
                        {{-- User Selection Dropdown --}}
                        <div class="col-md-6">
                            <label for="user_id" class="form-label small fw-semibold">Select User</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="" disabled selected>Choose a user...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Task Title Input --}}
                        <div class="col-md-6">
                            <label for="task_name" class="form-label small fw-semibold">Task Title</label>
                            <input type="text" name="task_name" id="task_name" class="form-control"
                                placeholder="e.g. Prepare Report" required>
                        </div>
                        {{-- Main Description (TinyMCE Rich Text Editor) --}}
                        <div class="col-12">
                            <label for="task_description" class="form-label small fw-semibold">Description</label>
                            <textarea name="task_description" id="task_description" class="form-control" rows="4"
                                placeholder="Task details..." required></textarea>
                        </div>
                        {{-- Additional Description (CKEditor5 Rich Text Editor) --}}
                        <div class="col-12">
                            <label for="task_description2" class="form-label small fw-semibold">Additional
                                Description</label>
                            <textarea name="task_description2" id="task_description2" class="form-control" rows="3"
                                placeholder="Additional task details (optional)..."></textarea>
                        </div>
                        {{-- Submit Button --}}
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-dark">
                                <i class="bi bi-send me-1"></i> Assign Task
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- All Tasks Management Section --}}
    <div>
        <h4 class="fw-semibold mb-3">All User Tasks</h4>
        <div class="table-responsive">
            {{-- DataTable for task management with AJAX actions --}}
            <table id="userTasksTable" class="table table-hover align-middle border table-sm">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Task</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Assigned</th>
                        <th>Completed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr>
                            {{-- Auto-numbered index column --}}
                            <td class="text-muted"></td>

                            {{-- Task Name with truncation --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="{{ $task->completed ? 'text-muted text-decoration-line-through' : '' }}"
                                        title="{{ $task->name }}">
                                        {{ Str::limit($task->name, 50) }}
                                    </span>
                                </div>
                            </td>

                            {{-- Task Status Badge --}}
                            <td>
                                @if ($task->completed)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill me-1"></i>Completed
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i>Pending
                                    </span>
                                @endif
                            </td>

                            {{-- Assigned User Information --}}
                            <td>
                                @if ($task->user)
                                    {{ $task->user->name }}
                                    <br>
                                    <small class="text-muted">{{ $task->user->email }}</small>
                                @else
                                    <span class="text-danger">[User not found]</span>
                                @endif
                            </td>

                            {{-- Task Creation Date --}}
                            <td>
                                <small>{{ $task->created_at->format('d M Y, h:i A') }}</small>
                            </td>

                            {{-- Task Completion Date --}}
                            <td>
                                @if ($task->completed && $task->completed_at)
                                    <small
                                        class="text-muted">{{ \Carbon\Carbon::parse($task->completed_at)->format('d M Y, h:i A') }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Action Buttons (View, Complete/Undo, Edit, Delete) --}}
                            <td>
                                <div class="d-flex gap-1">
                                    {{-- View Task Details Button --}}
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                        data-bs-target="#viewTaskModal" data-task-id="{{ $task->id }}"
                                        title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    {{-- Complete/Undo Task Button (AJAX) --}}
                                    <form method="POST"
                                        action="{{ $task->completed ? route('admin.tasks.undo', $task) : route('admin.tasks.complete', $task) }}"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm {{ $task->completed ? 'btn-outline-success' : 'btn-outline-success' }}"
                                            title="{{ $task->completed ? 'Mark as Incomplete' : 'Mark as Complete' }}">
                                            <i
                                                class="bi {{ $task->completed ? 'bi-arrow-counterclockwise' : 'bi-check2-circle' }}"></i>
                                        </button>
                                    </form>

                                    {{-- Edit Task Button --}}
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#editTaskModal" data-task-id="{{ $task->id }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    {{-- Delete Task Button (AJAX with confirmation) --}}
                                    <form method="POST" action="{{ route('admin.tasks.delete', $task) }}"
                                        class="delete-task-form d-inline">
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
                            <td class="text-center text-muted" colspan="7">No tasks found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal: View Task Details --}}
    <div class="modal fade" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Task Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Task Title Display --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Task Title</label>
                        <div class="border rounded p-3 bg-light" id="viewTaskName" style="line-height: 1.5;"></div>
                    </div>
                    {{-- Main Description Display (TinyMCE content) --}}
                    <div class="mb-0">
                        <label class="form-label fw-bold">Description</label>
                        <div class="border rounded p-3 bg-light" id="viewTaskDescription"
                            style="min-height: 100px; max-height: 500px; overflow-y: auto; white-space: normal; line-height: 1.5; word-wrap: break-word;">
                        </div>
                    </div>
                    {{-- Additional Description Display (CKEditor5 content) --}}
                    <div class="mb-0">
                        <label class="form-label fw-bold">Additional Description</label>
                        <div class="border rounded p-3 bg-light" id="viewTaskDescription2"
                            style="min-height: 50px; max-height: 300px; overflow-y: auto; white-space: pre-wrap; line-height: 1.5; word-wrap: break-word;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Edit Task --}}
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form method="POST" id="editTaskForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Edit Task Title --}}
                        <div class="mb-3">
                            <label for="editTaskName" class="form-label">Task Title</label>
                            <input type="text" name="name" id="editTaskName" class="form-control" required>
                        </div>
                        {{-- Edit Main Description (TinyMCE) --}}
                        <div class="mb-3">
                            <label for="editTaskDescription" class="form-label">Description</label>
                            <textarea name="description" id="editTaskDescription" class="form-control" rows="4"
                                placeholder="Enter task description..." required></textarea>
                        </div>
                        {{-- Edit Additional Description (CKEditor5) --}}
                        <div class="mb-3">
                            <label for="editTaskDescription2" class="form-label">Additional Description</label>
                            <textarea name="description2" id="editTaskDescription2" class="form-control" rows="3"
                                placeholder="Enter additional description (optional)..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Add New User --}}
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
                        {{-- User Name Input --}}
                        <div class="mb-3">
                            <label for="userName" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="userName" required>
                        </div>
                        {{-- User Email Input --}}
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="userEmail" required>
                        </div>
                        {{-- User Password Input --}}
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

    {{-- Modal: Image Zoom (for rich text content images) --}}
    <div class="image-zoom-modal" id="imageZoomModal">
        <img src="" alt="Zoomed Image" id="zoomedImage">
    </div>

    <!-- Modal: Add Product to User -->
    <div class="modal fade" id="addProductToUserModal" tabindex="-1" aria-labelledby="addProductToUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.products.assign') }}" enctype="multipart/form-data"
                id="addProductToUserForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductToUserModalLabel">Add Product to User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- User Selection -->
                        <div class="mb-3">
                            <label for="product_user_id" class="form-label">Select User</label>
                            <select name="user_id" id="product_user_id" class="form-select" required>
                                <option value="" disabled selected>Choose a user...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Product Image -->
                        <div class="mb-3">
                            <label for="product_image" class="form-label">Product Image</label>
                            <input type="file" class="form-control" name="image" id="product_image"
                                accept="image/*" required>
                        </div>
                        <!-- Product Price -->
                        <div class="mb-3">
                            <label for="product_price" class="form-label">Price</label>
                            <input type="number" class="form-control" name="price" id="product_price" step="0.01"
                                min="0" required>
                        </div>
                        <!-- Product Size -->
                        <div class="mb-3">
                            <label for="product_size" class="form-label">Size</label>
                            <input type="text" class="form-control" name="size" id="product_size" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

{{-- CSS Styles for Admin Dashboard --}}
@push('styles')
    {{-- External CSS Libraries --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />

    <style>
        /* Hover effect for statistic cards */
        .hover-scale:hover {
            transform: scale(1.02);
            transition: 0.2s ease-in-out;
        }

        /* Select2 dropdown styling */
        .select2-container--default .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        /* Loading spinner styles */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Button loading state */
        .btn:disabled {
            opacity: 0.65;
        }

        /* AJAX loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1);
            z-index: 9999;
            display: none;
        }

        /* Table row fade effect for AJAX updates */
        .table tbody tr {
            transition: opacity 0.3s ease;
        }

        /* Rich text content styling in view modal */
        #viewTaskDescription {
            font-family: Arial, sans-serif;
        }

        /* Styling for rich text elements */
        #viewTaskDescription h1,
        #viewTaskDescription h2,
        #viewTaskDescription h3 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        #viewTaskDescription p {
            margin-bottom: 0.5rem;
        }

        #viewTaskDescription ul,
        #viewTaskDescription ol {
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
        }

        #viewTaskDescription strong {
            font-weight: bold;
        }

        #viewTaskDescription em {
            font-style: italic;
        }

        /* Image styling in rich text content */
        #viewTaskDescription img {
            max-width: 100%;
            height: auto;
            border-radius: 0.375rem;
            margin: 0.5rem 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        #viewTaskDescription img:hover {
            transform: scale(1.02);
        }

        /* Image zoom modal styling */
        .image-zoom-modal {
            background-color: rgba(0, 0, 0, 0.9);
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            cursor: pointer;
        }

        .image-zoom-modal img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 95%;
            max-height: 95%;
            border-radius: 0.5rem;
        }

        /* TinyMCE container styling */
        .tox-tinymce {
            border-radius: 0.375rem !important;
        }

        /* Style for CKEditor5 tables in view modal */
        #viewTaskDescription2 table,
        #viewTaskDescription table {
            border-collapse: collapse;
            width: 100%;
            margin: 15px 0;
        }

        #viewTaskDescription2 table td,
        #viewTaskDescription2 table th,
        #viewTaskDescription table td,
        #viewTaskDescription table th {
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            text-align: left;
        }

        #viewTaskDescription2 table th,
        #viewTaskDescription table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        #viewTaskDescription2 table tbody tr:nth-child(even),
        #viewTaskDescription table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        #viewTaskDescription2 table tbody tr:hover,
        #viewTaskDescription table tbody tr:hover {
            background-color: #e9ecef;
        }

        /* Task table improvements */
        #userTasksTable {
            border-radius: 0.375rem;
            overflow: hidden;
        }

        #userTasksTable .btn-group .btn {
            margin-right: 2px;
        }

        #userTasksTable .btn-group .btn:last-child {
            margin-right: 0;
        }

        /* Badge improvements */
        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge i {
            font-size: 0.7rem;
        }

        /* Table action buttons styling */
        .d-flex.gap-1 .btn {
            border-radius: 0.375rem;
        }

        /* Strikethrough completed tasks */
        .text-decoration-line-through {
            opacity: 0.7;
        }
    </style>
@endpush

{{-- JavaScript Libraries and Custom Scripts --}}
@push('scripts')
    {{-- External JavaScript Libraries --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- DataTables Scripts --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    {{-- Rich Text Editors --}}
    <script src="https://cdn.tiny.cloud/1/96s1bjh0dbr79aoe5h20vpcele61qmaimpdu7rgotiln64xm/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    {{-- CKEditor5 --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        // Global variables for rich text editor instances
        let assignTaskEditor2; // CKEditor5 instance for assign task additional description
        let editTaskEditor2 = null; // CKEditor5 instance for edit task modal

        /**
         * Format date for display in table
         * @param {string} dateString - ISO date string
         * @returns {string} Formatted date string
         */
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        }

        /**
         * Fix image URLs in rich text content to use absolute paths
         * @param {string} content - HTML content with potentially relative image URLs
         * @returns {string} Content with fixed absolute image URLs
         */
        function fixImageUrls(content) {
            if (!content) return content;

            const baseUrl = '{{ config('app.url') }}';

            // Replace relative URLs starting with ../storage/ with absolute URLs
            content = content.replace(/src=["']\.\.\/storage\//g, `src="${baseUrl}/storage/`);

            // Replace relative URLs starting with storage/ with absolute URLs
            content = content.replace(/src=["'](?!https?:\/\/)storage\//g, `src="${baseUrl}/storage/`);

            return content;
        }

        /**
         * Update task row in DataTable after completion/undo action
         * @param {jQuery} taskRow - The table row element
         * @param {Object} task - Task object with updated data
         * @param {boolean} isCompleting - Whether task is being completed (true) or undone (false)
         */
        function updateTaskRowAfterCompletion(taskRow, task, isCompleting) {
            const nameCell = taskRow.find('td:nth-child(2)');
            const statusCell = taskRow.find('td:nth-child(3)');
            const completedCell = taskRow.find('td:nth-child(6)');
            const actionButtons = taskRow.find('td:nth-child(7)');

            // Update task name with completion styling
            if (isCompleting) {
                nameCell.find('span').addClass('text-muted text-decoration-line-through');
                nameCell.find('i.bi-check-circle-fill').remove();
            } else {
                nameCell.find('span').removeClass('text-muted text-decoration-line-through');
                nameCell.find('i.bi-check-circle-fill').remove();
            }

            // Update status badge
            if (isCompleting) {
                statusCell.html(
                    '<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Completed</span>');
            } else {
                statusCell.html('<span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pending</span>');
            }

            // Update completed date
            if (isCompleting && task.completed_at) {
                completedCell.html(`<small class="text-muted">${formatDate(task.completed_at)}</small>`);
            } else {
                completedCell.html('<span class="text-muted">-</span>');
            }

            // Update action button form action and styling
            const completeForm = actionButtons.find('form[action*="/complete"], form[action*="/undo"]');
            const completeBtn = completeForm.find('button[type="submit"]');

            if (isCompleting) {
                // Update form action from /complete to /undo
                const currentAction = completeForm.attr('action');
                const newAction = currentAction.replace('/complete', '/undo');
                completeForm.attr('action', newAction);

                // Update button styling and icon
                completeBtn.removeClass('btn-outline-success').addClass('btn-outline-success');
                completeBtn.attr('title', 'Mark as Incomplete');
                completeBtn.find('i').removeClass('bi-check2-circle').addClass('bi-arrow-counterclockwise');
            } else {
                // Update form action from /undo to /complete
                const currentAction = completeForm.attr('action');
                const newAction = currentAction.replace('/undo', '/complete');
                completeForm.attr('action', newAction);

                // Update button styling and icon
                completeBtn.removeClass('btn-outline-success').addClass('btn-outline-success');
                completeBtn.attr('title', 'Mark as Complete');
                completeBtn.find('i').removeClass('bi-arrow-counterclockwise').addClass('bi-check2-circle');
            }
        }

        function addNewTaskToTable(task) {
            if ($.fn.DataTable.isDataTable('#userTasksTable')) {
                const table = $('#userTasksTable').DataTable();

                // Generate URLs properly for new task
                const completeUrl = `{{ url('admin/tasks') }}/${task.id}/complete`;
                const deleteUrl = `{{ url('admin/tasks') }}/${task.id}/delete`;
                const csrfToken = `{{ csrf_token() }}`;

                // Create new row data array
                const newRow = [
                    '', // Index will be auto-filled by DataTable
                    `<div class="d-flex align-items-center">
                        <span title="${task.name}">${task.name.length > 50 ? task.name.substring(0, 50) + '...' : task.name}</span>
                    </div>`,
                    `<span class="badge bg-warning text-dark">
                        <i class="bi bi-clock me-1"></i>Pending
                    </span>`,
                    `${task.user.name}<br><small class="text-muted">${task.user.email}</small>`,
                    `<small>${formatDate(task.created_at)}</small>`,
                    `<span class="text-muted">-</span>`,
                    `<div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                            data-bs-target="#viewTaskModal" data-task-id="${task.id}" title="View Details">
                            <i class="bi bi-eye"></i>
                        </button>
                        <form method="POST" action="${completeUrl}" class="d-inline">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as Complete">
                                <i class="bi bi-check2-circle"></i>
                            </button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#editTaskModal" data-task-id="${task.id}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <form method="POST" action="${deleteUrl}" class="delete-task-form d-inline">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <button type="button" class="btn btn-sm btn-outline-danger delete-task-btn"
                                title="Delete" data-task-id="${task.id}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>`
                ];

                // Add row to DataTable and redraw
                table.row.add(newRow).draw();
            }
        }

        // Document ready - Initialize all components
        $(document).ready(function() {
            const $table = $('#userTasksTable');

            // Initialize DataTable if there are tasks to display
            if ($table.find('tbody tr').not(':has(td[colspan])').length > 0) {

                let table = $table.DataTable({
                    pageLength: 4, // Show 4 tasks per page
                    ordering: true,
                    order: [
                        [4, 'desc'] // Order by "Assigned" column (created_at) descending
                    ],
                    language: {
                        search: "Search tasks:",
                        lengthMenu: "Show _MENU_ tasks per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ tasks",
                        emptyTable: "No tasks available"
                    },
                    columnDefs: [{
                        orderable: false,
                        targets: [0, 6] // Disable sorting for '#' and Actions columns
                    }]
                });

                // Auto-fill row numbers in '#' column after any table operation
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

            // Initialize TinyMCE for Assign Task form
            if (document.querySelector('#task_description')) {
                tinymce.init({
                    selector: '#task_description',
                    height: 200,
                    menubar: false,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'table', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | ' +
                        'bold italic forecolor | alignleft aligncenter ' +
                        'alignright alignjustify | bullist numlist outdent indent | ' +
                        'link image | removeformat | help',
                    file_picker_types: 'image',
                    file_picker_callback: function(callback, value, meta) {
                        if (meta.filetype === 'image') {
                            const input = document.createElement('input');
                            input.setAttribute('type', 'file');
                            input.setAttribute('accept', 'image/*');
                            input.addEventListener('change', function(e) {
                                const file = e.target.files[0];
                                if (file) {
                                    // Validate file type
                                    if (!file.type.startsWith('image/')) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Invalid File Type',
                                            text: 'Please select an image file (JPG, PNG, GIF, etc.)',
                                            timer: 3000
                                        });
                                        return;
                                    }

                                    // Check file size (limit to 10MB)
                                    if (file.size > 10485760) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'File Too Large',
                                            text: 'Please select an image smaller than 10MB.',
                                            timer: 3000
                                        });
                                        return;
                                    }

                                    // Get CSRF token
                                    const token = document.querySelector(
                                            'meta[name="csrf-token"]')
                                        ?.getAttribute('content') ||
                                        document.querySelector('input[name="_token"]')?.value;

                                    if (!token) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Security Error',
                                            text: 'CSRF token not found. Please refresh the page.',
                                            timer: 3000
                                        });
                                        return;
                                    }

                                    // Create FormData and upload to server
                                    const formData = new FormData();
                                    formData.append('image', file);
                                    formData.append('_token', token);

                                    // Show loading indicator
                                    Swal.fire({
                                        title: 'Uploading image...',
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });

                                    // Upload to server
                                    fetch('{{ route('admin.upload.image') }}', {
                                            method: 'POST',
                                            body: formData,
                                            credentials: 'same-origin',
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error(
                                                    `HTTP error! status: ${response.status}`
                                                );
                                            }
                                            return response.json();
                                        })
                                        .then(data => {
                                            Swal.close();
                                            if (data.success) {
                                                callback(data.url, {
                                                    alt: file.name
                                                });
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Upload Failed',
                                                    text: data.message ||
                                                        'Failed to upload image',
                                                    timer: 3000
                                                });
                                            }
                                        })
                                        .catch(error => {
                                            Swal.close();
                                            console.error('Upload error:', error);
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Upload Error',
                                                text: 'Network error occurred while uploading image',
                                                timer: 3000
                                            });
                                        });
                                }
                            });
                            input.click();
                        }
                    },
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px; line-height:1.6; } img { max-width: 100%; height: auto; }',
                    relative_urls: false,
                    remove_script_host: false,
                    convert_urls: false,
                    setup: function(editor) {
                        editor.on('change', function() {
                            editor.save();
                        });
                    }
                });
            } else {
                console.error('Element #task_description not found for TinyMCE');
            }

            // Initialize CKEditor5 for Additional Description in Assign Task
            const assignTaskDesc2Element = document.querySelector('#task_description2');
            if (assignTaskDesc2Element) {
                ClassicEditor
                    .create(assignTaskDesc2Element, {
                        toolbar: [
                            'heading',
                            '|',
                            'bold',
                            'italic',
                            '|',
                            'numberedList',
                            'bulletedList',
                            '|',
                            'outdent',
                            'indent',
                            '|',
                            'link',
                            'blockQuote',
                            'insertTable',
                            '|',
                            'undo',
                            'redo'
                        ],
                        placeholder: 'Enter additional description...'
                    })
                    .then(editor => {
                        assignTaskEditor2 = editor;
                        console.log('CKEditor5 initialized for assign task additional description');
                    })
                    .catch(error => {
                        console.error('CKEditor5 initialization error:', error);
                    });
            } else {
                console.error('Element #task_description2 not found');
            }

            // Initialize TinyMCE for Edit Task Modal
            if (document.querySelector('#editTaskDescription')) {
                tinymce.init({
                    selector: '#editTaskDescription',
                    height: 300,
                    menubar: false,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'table', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | ' +
                        'bold italic forecolor | alignleft aligncenter ' +
                        'alignright alignjustify | bullist numlist outdent indent | ' +
                        'link image | removeformat | help',
                    file_picker_types: 'image',
                    file_picker_callback: function(callback, value, meta) {
                        if (meta.filetype === 'image') {
                            const input = document.createElement('input');
                            input.setAttribute('type', 'file');
                            input.setAttribute('accept', 'image/*');
                            input.addEventListener('change', function(e) {
                                const file = e.target.files[0];
                                if (file) {
                                    // Validate file type
                                    if (!file.type.startsWith('image/')) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Invalid File Type',
                                            text: 'Please select an image file (JPG, PNG, GIF, etc.)',
                                            timer: 3000
                                        });
                                        return;
                                    }

                                    // Check file size (limit to 10MB)
                                    if (file.size > 10485760) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'File Too Large',
                                            text: 'Please select an image smaller than 10MB.',
                                            timer: 3000
                                        });
                                        return;
                                    }

                                    // Get CSRF token
                                    const token = document.querySelector(
                                            'meta[name="csrf-token"]')
                                        ?.getAttribute('content') ||
                                        document.querySelector('input[name="_token"]')?.value;

                                    if (!token) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Security Error',
                                            text: 'CSRF token not found. Please refresh the page.',
                                            timer: 3000
                                        });
                                        return;
                                    }

                                    // Create FormData and upload to server
                                    const formData = new FormData();
                                    formData.append('image', file);
                                    formData.append('_token', token);

                                    // Show loading indicator
                                    Swal.fire({
                                        title: 'Uploading image...',
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });

                                    // Upload to server
                                    fetch('{{ route('admin.upload.image') }}', {
                                            method: 'POST',
                                            body: formData,
                                            credentials: 'same-origin',
                                            headers: {
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error(
                                                    `HTTP error! status: ${response.status}`
                                                );
                                            }
                                            return response.json();
                                        })
                                        .then(data => {
                                            Swal.close();
                                            if (data.success) {
                                                callback(data.url, {
                                                    alt: file.name
                                                });
                                            } else {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Upload Failed',
                                                    text: data.message ||
                                                        'Failed to upload image',
                                                    timer: 3000
                                                });
                                            }
                                        })
                                        .catch(error => {
                                            Swal.close();
                                            console.error('Upload error:', error);
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Upload Error',
                                                text: 'Network error occurred while uploading image',
                                                timer: 3000
                                            });
                                        });
                                }
                            });
                            input.click();
                        }
                    },
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px; line-height:1.6; } img { max-width: 100%; height: auto; }',
                    relative_urls: false,
                    remove_script_host: false,
                    convert_urls: false,
                    setup: function(editor) {
                        editor.on('change', function() {
                            editor.save();
                        });
                        editor.on('init', function() {
                            // Editor is ready for content
                        });
                    }
                });
            } else {
                console.error('Element #editTaskDescription not found for TinyMCE');
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

            // Delete Task Confirmation with AJAX (using event delegation)
            $(document).on('click', '.delete-task-btn', function(e) {
                e.preventDefault();

                const form = $(this).closest('form');
                const taskRow = $(this).closest('tr');

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
                        // AJAX deletion
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Task has been deleted successfully',
                                    confirmButtonColor: '#198754',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Remove row from table or reload
                                if ($.fn.DataTable.isDataTable('#userTasksTable')) {
                                    $('#userTasksTable').DataTable().row(taskRow)
                                        .remove().draw();
                                } else {
                                    taskRow.fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Failed to delete task';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: errorMessage,
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        });
                    }
                });
            });

            // Complete/Undo Task with AJAX
            $(document).on('submit', 'form[action*="/complete"], form[action*="/undo"]', function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const taskRow = form.closest('tr');
                const isCompleting = form.attr('action').includes('/complete');

                // AJAX submission
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        const action = isCompleting ? 'completed' : 'marked as pending';

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: `Task ${action} successfully`,
                            confirmButtonColor: '#198754',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update the task row without reload
                        updateTaskRowAfterCompletion(taskRow, response.task, isCompleting);
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });

                return false;
            });


            // Assign Task Form Validation with AJAX
            $('form[action="{{ route('admin.tasks.assign') }}"]').attr('id', 'assignTaskForm');

            $('#assignTaskForm').validate({
                rules: {
                    user_id: {
                        required: true
                    },
                    task_name: {
                        required: true,
                        minlength: 3
                    },
                    task_description: {
                        required: function() {
                            if (tinymce.get('task_description')) {
                                const content = tinymce.get('task_description').getContent();
                                return content.trim() === '' || content === '<p></p>' || content ===
                                    '<p><br></p>';
                            }
                            return $('#task_description').val().trim() === '';
                        },
                        maxlength: 16777215 // 16MB limit for images
                    },
                    task_description2: {
                        maxlength: 16777215 // 16MB limit for images
                    }
                },
                messages: {
                    user_id: "Please select a user.",
                    task_name: {
                        required: "Please enter a task title.",
                        minlength: "Task title must be at least 3 characters."
                    },
                    task_description: {
                        required: "Please enter a description.",
                        maxlength: "Content too large. Maximum 16MB allowed including images."
                    }
                },
                errorClass: 'text-danger small',
                errorElement: 'div',
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    // Ensure TinyMCE content is saved before submission
                    if (tinymce.get('task_description')) {
                        tinymce.get('task_description').save();
                    }

                    // Ensure CKEditor5 content is saved before submission
                    if (assignTaskEditor2) {
                        // Get CKEditor5 data and set it to the textarea
                        document.querySelector('#task_description2').value = assignTaskEditor2
                            .getData();
                    }

                    // AJAX submission
                    const formData = new FormData(form);
                    const submitBtn = $(form).find('button[type="submit"]');
                    const originalText = submitBtn.html();

                    // Show loading state
                    submitBtn.prop('disabled', true).html(
                        '<i class="spinner-border spinner-border-sm me-1"></i> Assigning...');

                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Task assigned successfully',
                                confirmButtonColor: '#198754'
                            });

                            // Reset form
                            form.reset();
                            $('#user_id').val(null).trigger('change'); // Reset Select2
                            if (tinymce.get('task_description')) {
                                tinymce.get('task_description').setContent('');
                            }
                            if (assignTaskEditor2) {
                                assignTaskEditor2.setData('');
                            }

                            // Add new task to DataTable without reload
                            if (response.task) {
                                addNewTaskToTable(response.task);
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                                confirmButtonColor: '#dc3545'
                            });
                        },
                        complete: function() {
                            // Restore button
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    });

                    return false; // Prevent default form submission
                }
            });

            // Add User Form Validation with AJAX
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
                },
                submitHandler: function(form) {
                    // AJAX submission
                    const formData = new FormData(form);
                    const submitBtn = $(form).find('button[type="submit"]');
                    const originalText = submitBtn.html();

                    // Show loading state
                    submitBtn.prop('disabled', true).html(
                        '<i class="spinner-border spinner-border-sm me-1"></i> Creating...');

                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'User created successfully',
                                confirmButtonColor: '#198754'
                            });

                            // Reset form and close modal
                            form.reset();
                            $('#addUserModal').modal('hide');

                            // Update user dropdown and count without full reload
                            if (response.user) {
                                // Add new user to the select dropdown
                                const newOption = new Option(
                                    `${response.user.name} (${response.user.email})`,
                                    response.user.id);
                                $('#user_id').append(newOption);

                                // Update user count in the dashboard card
                                const userCountCard = $('.card-body h3.text-primary');
                                if (userCountCard.length) {
                                    const currentCount = parseInt(userCountCard.text()) ||
                                        0;
                                    userCountCard.text(currentCount + 1);
                                }
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'An error occurred';
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                const errors = Object.values(xhr.responseJSON.errors)
                                    .flat();
                                errorMessage = errors.join('<br>');
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                html: errorMessage,
                                confirmButtonColor: '#dc3545'
                            });
                        },
                        complete: function() {
                            // Restore button
                            submitBtn.prop('disabled', false).html(originalText);
                        }
                    });

                    return false; // Prevent default form submission
                }
            });

            // Edit Task Modal
            const editTaskModal = document.getElementById('editTaskModal');

            // Initialize CKEditor5 for Edit Task modal when shown
            $('#editTaskModal').on('shown.bs.modal', function() {
                console.log('Edit Task modal shown, checking CKEditor5...');
                // Only initialize if not already done
                if (!editTaskEditor2) {
                    const element = document.querySelector('#editTaskDescription2');
                    console.log('Edit modal element found:', element);
                    console.log('ClassicEditor available:', typeof ClassicEditor !== 'undefined');

                    if (element && typeof ClassicEditor !== 'undefined') {
                        console.log('Initializing CKEditor5 for edit modal...');
                        ClassicEditor
                            .create(element, {
                                toolbar: [
                                    'heading',
                                    '|',
                                    'bold',
                                    'italic',
                                    '|',
                                    'numberedList',
                                    'bulletedList',
                                    '|',
                                    'outdent',
                                    'indent',
                                    '|',
                                    'link',
                                    'blockQuote',
                                    'insertTable',
                                    '|',
                                    'undo',
                                    'redo'
                                ],
                                placeholder: 'Enter additional description...'
                            })
                            .then(editor => {
                                editTaskEditor2 = editor;
                                console.log('CKEditor5 initialized successfully for edit modal');

                                // Set the data from the stored value if available
                                const storedDescription2 = $(element).data('pending-content');
                                if (storedDescription2) {
                                    editor.setData(storedDescription2);
                                    $(element).removeData('pending-content');
                                }
                            })
                            .catch(error => {
                                console.error('CKEditor5 initialization error for edit modal:', error);
                            });
                    } else {
                        console.error(
                            'CKEditor5 element not found or ClassicEditor not available for edit modal');

                        // Try again after a short delay in case the script is still loading
                        const element = document.querySelector('#editTaskDescription2');
                        if (element && typeof ClassicEditor === 'undefined') {
                            console.log('Retrying CKEditor5 initialization for edit modal after delay...');
                            setTimeout(() => {
                                if (typeof ClassicEditor !== 'undefined') {
                                    console.log(
                                        'CKEditor5 now available for edit modal, initializing...'
                                    );
                                    ClassicEditor
                                        .create(element, {
                                            toolbar: [
                                                'heading',
                                                '|',
                                                'bold',
                                                'italic',
                                                '|',
                                                'numberedList',
                                                'bulletedList',
                                                '|',
                                                'outdent',
                                                'indent',
                                                '|',
                                                'link',
                                                'blockQuote',
                                                'insertTable',
                                                '|',
                                                'undo',
                                                'redo'
                                            ],
                                            placeholder: 'Enter additional description...'
                                        })
                                        .then(editor => {
                                            editTaskEditor2 = editor;
                                            console.log(
                                                'CKEditor5 initialized successfully for edit modal (delayed)'
                                            );

                                            // Set the data from the stored value if available
                                            const storedDescription2 = $(element).data(
                                                'pending-content');
                                            if (storedDescription2) {
                                                editor.setData(storedDescription2);
                                                $(element).removeData('pending-content');
                                            }
                                        })
                                        .catch(error => {
                                            console.error(
                                                'CKEditor5 delayed initialization error for edit modal:',
                                                error);
                                        });
                                } else {
                                    console.error(
                                        'CKEditor5 still not available for edit modal after delay'
                                    );
                                }
                            }, 1000);
                        }
                    }
                } else {
                    console.log('CKEditor5 already initialized for edit modal');
                    // Set the data from the stored value if available
                    const element = document.querySelector('#editTaskDescription2');
                    const storedDescription2 = $(element).data('pending-content');
                    if (storedDescription2) {
                        editTaskEditor2.setData(storedDescription2);
                        $(element).removeData('pending-content');
                    }
                }
            });

            // Clean up CKEditor5 when edit modal is hidden
            $('#editTaskModal').on('hidden.bs.modal', function() {
                if (editTaskEditor2) {
                    editTaskEditor2.destroy().then(() => {
                        editTaskEditor2 = null;
                        console.log('CKEditor5 destroyed for edit modal');
                    }).catch(error => {
                        console.error('Error destroying CKEditor5 for edit modal:', error);
                        editTaskEditor2 = null;
                    });
                }

                // Clear any pending content data
                const element = document.querySelector('#editTaskDescription2');
                if (element) {
                    $(element).removeData('pending-content');
                }
            });

            editTaskModal.addEventListener('show.bs.modal', function(event) {
                console.log('Edit modal opening...');
                const button = event.relatedTarget;
                const taskId = button.getAttribute('data-task-id');
                console.log('Task ID:', taskId);

                const nameInput = editTaskModal.querySelector('#editTaskName');
                const descriptionInput = editTaskModal.querySelector('#editTaskDescription');
                const description2Input = editTaskModal.querySelector('#editTaskDescription2');
                const form = editTaskModal.querySelector('#editTaskForm');

                form.action = `{{ url('admin/tasks') }}/${taskId}/edit`;

                // Fetch task data via AJAX to get unescaped HTML content
                $.ajax({
                    url: `{{ url('admin/api/tasks') }}/${taskId}`,
                    method: 'GET',
                    success: function(response) {
                        console.log('API Response:', response);
                        const task = response.task;

                        nameInput.value = task.name;

                        // Store the additional description data temporarily for when the editor is initialized
                        const editDesc2Element = document.querySelector(
                            '#editTaskDescription2');
                        if (editTaskEditor2) {
                            editTaskEditor2.setData(task.description2 || '');
                        } else {
                            // Store the data temporarily for when the editor is initialized
                            $(editDesc2Element).data('pending-content', task.description2 ||
                                '');
                            editDesc2Element.value = task.description2 || '';
                        }

                        // Wait a bit for modal to be fully shown, then set TinyMCE content
                        setTimeout(function() {
                            if (tinymce.get('editTaskDescription')) {
                                tinymce.get('editTaskDescription').setContent(task
                                    .description || '');
                            } else {
                                descriptionInput.value = task.description || '';
                            }
                        }, 100);
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch task data:', xhr);
                        nameInput.value = 'Error loading task';
                        descriptionInput.value = 'Failed to load task data';
                        if (editTaskEditor2) {
                            editTaskEditor2.setData('Failed to load task data');
                        }
                    }
                });

                // Apply validation when modal is shown
                $('#editTaskForm').validate({
                    rules: {
                        name: {
                            required: true,
                            minlength: 3
                        },
                        description: {
                            required: function() {
                                if (tinymce.get('editTaskDescription')) {
                                    const content = tinymce.get('editTaskDescription')
                                        .getContent();
                                    return content.trim() === '' || content === '<p></p>' ||
                                        content === '<p><br></p>';
                                }
                                return $('#editTaskDescription').val().trim() === '';
                            },
                            maxlength: 16777215 // 16MB limit for images
                        },
                        description2: {
                            maxlength: 16777215 // 16MB limit for images
                        }
                    },
                    messages: {
                        name: {
                            required: "Task title is required.",
                            minlength: "Task title must be at least 3 characters."
                        },
                        description: {
                            required: "Description is required.",
                            maxlength: "Content too large. Maximum 16MB allowed including images."
                        }
                    },
                    errorClass: 'text-danger small',
                    errorElement: 'div',
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function(form) {
                        // Ensure TinyMCE content is saved before submission
                        if (tinymce.get('editTaskDescription')) {
                            tinymce.get('editTaskDescription').save();
                        }

                        // Ensure CKEditor5 content is saved before submission
                        if (editTaskEditor2) {
                            // Get CKEditor5 data and set it to the textarea
                            document.querySelector('#editTaskDescription2').value =
                                editTaskEditor2.getData();
                        }

                        // AJAX submission
                        const formData = new FormData(form);
                        const submitBtn = $(form).find('button[type="submit"]');
                        const originalText = submitBtn.html();

                        // Show loading state
                        submitBtn.prop('disabled', true).html(
                            '<i class="spinner-border spinner-border-sm me-1"></i> Saving...'
                        );

                        $.ajax({
                            url: $(form).attr('action'),
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Task updated successfully',
                                    confirmButtonColor: '#198754'
                                });

                                // Close modal
                                $('#editTaskModal').modal('hide');

                                // Update the task row in the table without reload
                                const taskRow = $(
                                    `button[data-task-id="${response.task.id}"]`
                                ).closest('tr');
                                if (taskRow.length) {
                                    // Update task name in the table
                                    const nameCell = taskRow.find(
                                        'td:nth-child(2)');
                                    const taskName = response.task.name;
                                    const displayName = taskName.length > 50 ?
                                        taskName.substring(0, 50) + '...' :
                                        taskName;
                                    nameCell.find('span').attr('title', taskName)
                                        .text(displayName);

                                    // Update view and edit button data attributes
                                    taskRow.find(
                                        'button[data-bs-target="#viewTaskModal"]'
                                    ).attr('data-task-id', response.task.id);
                                    taskRow.find(
                                        'button[data-bs-target="#editTaskModal"]'
                                    ).attr('data-task-id', response.task.id);
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'An error occurred';
                                if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    const errors = Object.values(xhr.responseJSON
                                        .errors).flat();
                                    errorMessage = errors.join('<br>');
                                } else if (xhr.responseJSON && xhr.responseJSON
                                    .message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    html: errorMessage,
                                    confirmButtonColor: '#dc3545'
                                });
                            },
                            complete: function() {
                                // Restore button
                                submitBtn.prop('disabled', false).html(
                                    originalText);
                            }
                        });

                        return false; // Prevent default form submission
                    }
                });
            });

            // Clean up validation when modal is hidden
            editTaskModal.addEventListener('hidden.bs.modal', function() {
                $('#editTaskForm').removeData('validator');
            });

            // View Task Modal
            const viewTaskModal = document.getElementById('viewTaskModal');
            viewTaskModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const taskId = button.getAttribute('data-task-id');

                // Fetch task data via AJAX to get unescaped HTML content
                $.ajax({
                    url: `{{ url('admin/api/tasks') }}/${taskId}`,
                    method: 'GET',
                    success: function(response) {
                        const task = response.task;

                        const nameElement = viewTaskModal.querySelector('#viewTaskName');
                        const descriptionElement = viewTaskModal.querySelector(
                            '#viewTaskDescription');
                        const description2Element = viewTaskModal.querySelector(
                            '#viewTaskDescription2');

                        nameElement.textContent = task.name;

                        // Set description content as HTML with fixed image URLs
                        if (task.description && task.description.trim() !== '') {
                            descriptionElement.innerHTML = fixImageUrls(task.description);
                        } else {
                            descriptionElement.innerHTML =
                                '<p class="text-muted"><em>No description available</em></p>';
                        }

                        // Set additional description content as HTML with fixed image URLs
                        if (task.description2 && task.description2.trim() !== '') {
                            description2Element.innerHTML = fixImageUrls(task.description2);
                        } else {
                            description2Element.innerHTML =
                                '<p class="text-muted"><em>No additional description</em></p>';
                        }

                        // Add image click handlers for zoom functionality
                        setTimeout(function() {
                            $('#viewTaskDescription img, #viewTaskDescription2 img')
                                .off('click').on(
                                    'click',
                                    function(e) {
                                        e.stopPropagation();
                                        const imgSrc = $(this).attr('src');
                                        $('#zoomedImage').attr('src', imgSrc);
                                        $('#imageZoomModal').fadeIn(200);
                                    });
                        }, 100);
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch task data:', xhr);
                        const nameElement = viewTaskModal.querySelector('#viewTaskName');
                        const descriptionElement = viewTaskModal.querySelector(
                            '#viewTaskDescription');
                        const description2Element = viewTaskModal.querySelector(
                            '#viewTaskDescription2');

                        nameElement.textContent = 'Error loading task';
                        descriptionElement.innerHTML =
                            '<p class="text-danger"><em>Failed to load task data</em></p>';
                        description2Element.innerHTML =
                            '<p class="text-danger"><em>Failed to load task data</em></p>';
                    }
                });
            });

            // Image zoom modal close functionality
            $('#imageZoomModal').on('click', function() {
                $(this).fadeOut(200);
            });

            // Keyboard support for image zoom
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#imageZoomModal').is(':visible')) {
                    $('#imageZoomModal').fadeOut(200);
                }
            });
        });
    </script>
@endpush
