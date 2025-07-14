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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js" referrerpolicy="origin"></script>

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

        /* Rich text content styling */
        #viewTaskDescription {
            font-family: Arial, sans-serif;
        }

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

        /* Enhanced form styling */
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .form-control-lg:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
        }

        /* Modal enhancements */
        .modal-content {
            border-radius: 1rem;
            overflow: hidden;
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: none;
            padding: 1.5rem;
        }

        /* Alert styling */
        .alert-light {
            background-color: rgba(13, 110, 253, 0.05);
            border-color: rgba(13, 110, 253, 0.2);
        }

        /* Character count styling */
        #charCount {
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }

        /* Button animations */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        /* Tips section styling */
        .alert h6 {
            color: #0d6efd;
        }

        .alert ul li {
            margin-bottom: 0.25rem;
        }

        /* Task text clickable styling */
        .task-text {
            cursor: pointer;
            color: #0d6efd;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .task-text:hover {
            color: #0a58ca;
            text-decoration: underline;
        }
    </style>
</head>

<body class="bg-light">
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

                <!-- Add Task Button -->
                <div class="mb-4 text-center">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Task
                    </button>
                </div>

                <!-- Summary Cards -->
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
                                <h4 class="fw-bold text-primary">{{ $pendingTasks }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task List -->
                <ul class="list-group" id="taskList">
                    @foreach ($tasks as $task)
                        <li class="list-group-item" id="task-{{ $task->id }}">
                            @if (!$task->completed)
                                <p class="mb-2">
                                    <span class="task-text" data-id="{{ $task->id }}"
                                        data-name="{{ $task->name }}" data-description="{{ $task->description }}">
                                        {{ $task->name }}
                                    </span>
                                </p>
                            @else
                                <p class="mb-2 completed">{{ $task->name }}</p>
                            @endif

                            <small class="text-muted d-block ms-1">
                                Created: {{ $task->created_at->format('M d, Y h:i A') }}
                                @if ($task->completed_at)
                                    | Completed: {{ $task->completed_at->format('M d, Y h:i A') }}
                                @endif
                            </small>

                            <div class="d-flex mt-2">
                                <button class="btn btn-sm btn-outline-info me-2 view-btn" data-id="{{ $task->id }}"
                                    data-name="{{ $task->name }}" data-description="{{ $task->description }}"
                                    title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success me-2 complete-btn"
                                    data-id="{{ $task->id }}"
                                    title="{{ $task->completed ? 'Undo' : 'Complete' }}">
                                    <i
                                        class="bi {{ $task->completed ? 'bi-arrow-counterclockwise' : 'bi-check2-circle' }}"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $task->id }}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <form id="addTaskForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="taskName" class="form-label">Task Heading</label>
                        <input type="text" class="form-control" id="taskName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="taskDescription" name="description" rows="3"
                            placeholder="Enter task description..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-save me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <form id="modalEditForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editTaskId">
                    <div class="mb-3">
                        <label for="editTaskName" class="form-label">Task Heading</label>
                        <input type="text" class="form-control" id="editTaskName" name="editTaskName" required>
                    </div>
                    <div class="mb-3">
                        <label for="editTaskDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editTaskDescription" name="editTaskDescription" rows="3"
                            placeholder="Enter task description..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-save me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Task Modal -->
    <div class="modal fade" id="viewTaskModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="viewEditForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Task Heading</label>
                            <p class="form-control-plaintext border rounded p-2 bg-light" id="viewTaskName"></p>
                            <input type="text" class="form-control d-none" id="editViewTaskName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <div class="border rounded p-3 bg-light" id="viewTaskDescription"
                                style="min-height: 100px; max-height: 500px; overflow-y: auto; white-space: normal; line-height: 1.5; word-wrap: break-word;">
                            </div>
                            <textarea class="form-control d-none" id="editViewTaskDescription" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                        <button type="button" class="btn btn-warning" id="viewEditBtn">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="cancelEditBtn">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-success d-none" id="saveEditBtn">
                            <i class="bi bi-save me-1"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image Zoom Modal -->
    <div class="image-zoom-modal" id="imageZoomModal">
        <img src="" alt="Zoomed Image" id="zoomedImage">
    </div>

    <!-- JS -->
    <script>
        $(function() {
            const token = $('meta[name="csrf-token"]').attr('content');

            const addModal = new bootstrap.Modal(document.getElementById('addTaskModal'));
            const editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
            const viewModal = new bootstrap.Modal(document.getElementById('viewTaskModal'));

            // Initialize TinyMCE for description fields with error handling
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '#taskDescription, #editTaskDescription',
                    height: 300,
                    menubar: false,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'help', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
                        'alignleft aligncenter alignright alignjustify | ' +
                        'bullist numlist outdent indent | link image | removeformat | help',
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
                                    }); // Upload to server
                                    fetch('{{ route('upload.image') }}', {
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
                    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height:1.6; } img { max-width: 100%; height: auto; }',
                    branding: false,
                    promotion: false,
                    setup: function(editor) {
                        editor.on('change', function() {
                            editor.save();
                        });
                    }
                });
            } else {
                console.warn('TinyMCE is not loaded. Rich text editing will not be available.');
            }

            function formatDate(dateStr) {
                const d = new Date(dateStr);
                return d.toLocaleString();
            }

            // Function to update task counters
            function updateTaskCounters() {
                const totalTasks = $('#taskList li').length;
                const completedTasks = $('#taskList li').filter(function() {
                    return $(this).find('.completed').length > 0;
                }).length;
                const pendingTasks = totalTasks - completedTasks;

                // Update the counter displays
                $('.card-body h4').eq(0).text(totalTasks);
                $('.card-body h4').eq(1).text(completedTasks);
                $('.card-body h4').eq(2).text(pendingTasks);
            }

            // Add Task
            $('#addTaskForm').on('submit', function(e) {
                e.preventDefault();
                const name = $('#taskName').val().trim();

                // Get description from TinyMCE if available, otherwise from textarea
                let description = '';
                const tinyMCEInstance = tinymce.get('taskDescription');
                if (tinyMCEInstance) {
                    description = tinyMCEInstance.getContent().trim();
                } else {
                    description = $('#taskDescription').val().trim();
                }

                if (!name || !description) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Please fill in all fields',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                $.post("{{ route('tasks.store') }}", {
                    name,
                    description,
                    _token: token
                }, function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Task Added!',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Create DOM elements properly
                        const $taskItem = $(`
                            <li class="list-group-item" id="task-${res.task.id}">
                                <p class="mb-2">
                                    <span class="task-text">${res.task.name}</span>
                                </p>
                                <small class="text-muted d-block ms-1">
                                    Created: ${formatDate(res.task.created_at)}
                                </small>
                                <div class="d-flex mt-2">
                                    <button class="btn btn-sm btn-outline-info me-2 view-btn" title="View">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-success me-2 complete-btn" title="Complete">
                                        <i class="bi bi-check2-circle"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger delete-btn">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </li>
                        `);

                        // Set data attributes properly
                        $taskItem.find('.task-text')
                            .data('id', res.task.id)
                            .data('name', res.task.name)
                            .data('description', res.task.description);

                        $taskItem.find('.view-btn')
                            .data('id', res.task.id)
                            .data('name', res.task.name)
                            .data('description', res.task.description);

                        $taskItem.find('.complete-btn, .delete-btn')
                            .data('id', res.task.id);

                        $('#taskList').prepend($taskItem);
                        $('#taskName').val('');

                        // Clear TinyMCE content if available, otherwise clear textarea
                        const tinyMCEInstance = tinymce.get('taskDescription');
                        if (tinyMCEInstance) {
                            tinyMCEInstance.setContent('');
                        } else {
                            $('#taskDescription').val('');
                        }

                        addModal.hide();

                        // Update task counters
                        updateTaskCounters();
                    }
                }).fail(function(xhr) {
                    let errorMessage = 'Failed to add task';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join(', ');
                    } else if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            // Error parsing response
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error Adding Task',
                        text: errorMessage,
                        timer: 5000,
                        showConfirmButton: true
                    });
                });
            });

            // Edit Task Modal functionality - Click on task text to edit
            $(document).on('click', '.task-text', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description');

                // Check if task is completed
                const taskItem = $(`#task-${id}`);
                const isCompleted = taskItem.find('.completed').length > 0;

                if (isCompleted) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Cannot Edit Completed Task',
                        text: 'Please mark the task as pending first to edit it.',
                        timer: 3000
                    });
                    return;
                }

                // Populate the edit modal
                $('#editTaskId').val(id);
                $('#editTaskName').val(name);

                // Set description in TinyMCE if available, otherwise in textarea
                const editTinyMCEInstance = tinymce.get('editTaskDescription');
                if (editTinyMCEInstance) {
                    editTinyMCEInstance.setContent(description || '');
                } else {
                    $('#editTaskDescription').val(description || '');
                }

                editModal.show();
            });

            // Edit Task Form Submission
            $('#modalEditForm').on('submit', function(e) {
                e.preventDefault();

                const id = $('#editTaskId').val();
                const name = $('#editTaskName').val().trim();

                // Get description from TinyMCE if available, otherwise from textarea
                let description = '';
                const editTinyMCEInstance = tinymce.get('editTaskDescription');
                if (editTinyMCEInstance) {
                    description = editTinyMCEInstance.getContent().trim();
                } else {
                    description = $('#editTaskDescription').val().trim();
                }

                if (!name || !description) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Please fill in all fields',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                $.post(`tasks/${id}/edit`, {
                    _token: token,
                    name,
                    description
                }, function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Task Updated!',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update the task in the list
                        const taskItem = $(`#task-${id}`);
                        taskItem.find('.task-text').text(name).data('name', name).data(
                            'description', description);
                        taskItem.find('.view-btn').data('name', name).data('description',
                            description);

                        // Clear and hide modal
                        $('#editTaskId').val('');
                        $('#editTaskName').val('');

                        if (editTinyMCEInstance) {
                            editTinyMCEInstance.setContent('');
                        } else {
                            $('#editTaskDescription').val('');
                        }

                        editModal.hide();
                    }
                }).fail(function(xhr) {
                    let errorMessage = 'Failed to update task';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join(', ');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: errorMessage,
                        showConfirmButton: true
                    });
                });
            });

            // View Task
            $(document).on('click', '.view-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description');

                // Check if task is completed by looking at the parent task item
                const taskItem = $(`#task-${id}`);
                const isCompleted = taskItem.find('.completed').length > 0;

                $('#viewTaskName').text(name);

                // If we have a description, show it, otherwise show no description message
                if (description && description.trim() !== '') {
                    $('#viewTaskDescription').html(description);
                } else {
                    $('#viewTaskDescription').html(
                        '<p class="text-muted"><em>No description available</em></p>');
                }

                // Store task data for edit functionality
                $('#viewEditBtn').data('id', id).data('name', name).data('description', description);

                // Hide/show edit button based on completion status
                if (isCompleted) {
                    $('#viewEditBtn').hide();
                } else {
                    $('#viewEditBtn').show();
                }

                viewModal.show();

                // Add image click handlers for zoom functionality after modal is shown
                setTimeout(function() {
                    $('#viewTaskDescription img').off('click').on('click', function(e) {
                        e.stopPropagation();
                        const imgSrc = $(this).attr('src');
                        $('#zoomedImage').attr('src', imgSrc);
                        $('#imageZoomModal').fadeIn(200);
                    });

                    // Handle broken images
                    $('#viewTaskDescription img').off('error').on('error', function() {
                        $(this).attr('alt', 'Image not found').css({
                            'background-color': '#f8f9fa',
                            'border': '1px dashed #dee2e6',
                            'padding': '20px',
                            'text-align': 'center',
                            'color': '#6c757d'
                        });
                    });
                }, 300);
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

            // Global error handler for images
            $(document).on('error', 'img', function() {
                const $img = $(this);
                if (!$img.hasClass('error-handled')) {
                    $img.addClass('error-handled');
                    $img.attr('alt', 'Image not found');
                    $img.css({
                        'background-color': '#f8f9fa',
                        'border': '1px dashed #dee2e6',
                        'padding': '10px',
                        'text-align': 'center',
                        'color': '#6c757d',
                        'min-height': '100px',
                        'display': 'flex',
                        'align-items': 'center',
                        'justify-content': 'center'
                    });
                    $img.attr('src', 'data:image/svg+xml;base64,' + btoa(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="#f8f9fa" stroke="#dee2e6" stroke-dasharray="5,5"/><text x="50" y="50" text-anchor="middle" dy=".3em" fill="#6c757d" font-family="Arial, sans-serif" font-size="12">Image not found</text></svg>'
                        ));
                }
            });

            // Edit from View Modal - WITH TINYMCE
            $('#viewEditBtn').on('click', function() {
                // Switch to edit mode
                $('#viewTaskName').addClass('d-none');
                $('#viewTaskDescription').addClass('d-none');
                $('#editViewTaskName').removeClass('d-none').val($(this).data('name'));
                $('#editViewTaskDescription').removeClass('d-none');

                // Initialize TinyMCE for this specific textarea with error handling
                if (typeof tinymce !== 'undefined') {
                    tinymce.init({
                        selector: '#editViewTaskDescription',
                        height: 300,
                        menubar: false,
                        plugins: [
                            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                            'preview',
                            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                            'insertdatetime', 'media', 'table', 'help', 'wordcount'
                        ],
                        toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
                            'alignleft aligncenter alignright alignjustify | ' +
                            'bullist numlist outdent indent | link image | removeformat | help',
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
                                        fetch('{{ route('upload.image') }}', {
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
                                                        text: data
                                                            .message ||
                                                            'Failed to upload image',
                                                        timer: 3000
                                                    });
                                                }
                                            })
                                            .catch(error => {
                                                Swal.close();
                                                console.error('Upload error:',
                                                    error);
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
                        content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; line-height:1.6; } img { max-width: 100%; height: auto; }',
                        branding: false,
                        promotion: false,
                        setup: function(editor) {
                            editor.on('init', function() {
                                // Set content after editor is initialized
                                editor.setContent($('#viewEditBtn').data(
                                    'description'));
                            });
                            editor.on('change', function() {
                                editor.save();
                            });
                        }
                    });
                } else {
                    // Fallback if TinyMCE is not available
                    $('#editViewTaskDescription').val($('#viewEditBtn').data('description'));
                }

                // Show/hide buttons
                $('#viewEditBtn').addClass('d-none');
                $('#cancelEditBtn').removeClass('d-none');
                $('#saveEditBtn').removeClass('d-none');
            });

            // Cancel Edit in View Modal - WITH TINYMCE
            $('#cancelEditBtn').on('click', function() {
                // Destroy TinyMCE instance if it exists
                if (tinymce.get('editViewTaskDescription')) {
                    tinymce.get('editViewTaskDescription').destroy();
                }

                // Switch back to view mode
                $('#viewTaskName').removeClass('d-none');
                $('#viewTaskDescription').removeClass('d-none');
                $('#editViewTaskName').addClass('d-none');
                $('#editViewTaskDescription').addClass('d-none');

                // Show/hide buttons
                $('#viewEditBtn').removeClass('d-none');
                $('#cancelEditBtn').addClass('d-none');
                $('#saveEditBtn').addClass('d-none');
            });



            // Save Edit in View Modal - WITH TINYMCE
            $('#viewEditForm').on('submit', function(e) {
                e.preventDefault();

                const id = $('#viewEditBtn').data('id');
                const name = $('#editViewTaskName').val().trim();
                let description = '';

                // Get content from TinyMCE if it exists, otherwise from textarea
                if (tinymce.get('editViewTaskDescription')) {
                    description = tinymce.get('editViewTaskDescription').getContent().trim();
                } else {
                    description = $('#editViewTaskDescription').val().trim();
                }

                if (!name || !description) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Please fill in all fields',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                $.post(`tasks/${id}/edit`, {
                    _token: token,
                    name,
                    description
                }, function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Task Updated!',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update the task in the list
                        const taskItem = $(`#task-${id}`);
                        taskItem.find('.task-text').text(name);
                        taskItem.find('.view-btn').data('name', name).data('description',
                            description);

                        // Update the view modal display
                        $('#viewTaskName').text(name);
                        $('#viewTaskDescription').html(description);
                        $('#viewEditBtn').data('name', name).data('description', description);

                        // Destroy TinyMCE instance if it exists
                        if (tinymce.get('editViewTaskDescription')) {
                            tinymce.get('editViewTaskDescription').destroy();
                        }

                        // Switch back to view mode
                        $('#viewTaskName').removeClass('d-none');
                        $('#viewTaskDescription').removeClass('d-none');
                        $('#editViewTaskName').addClass('d-none');
                        $('#editViewTaskDescription').addClass('d-none');

                        // Show/hide buttons
                        $('#viewEditBtn').removeClass('d-none');
                        $('#cancelEditBtn').addClass('d-none');
                        $('#saveEditBtn').addClass('d-none');
                    }
                }).fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: 'Failed to update task',
                        showConfirmButton: true
                    });
                });
            });

            // Complete / Undo
            $(document).on('click', '.complete-btn', function() {
                const id = $(this).data('id');

                $.post(`tasks/${id}/complete`, {
                    _token: token
                }, function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: res.completed ? 'Marked as Completed!' :
                                'Marked as Pending!',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        const taskItem = $(`#task-${id}`);
                        const name = res.name; // Get name from server response
                        const description = res.description; // Get description from server response

                        if (res.completed) {
                            // Create completed task structure
                            const $completedTask = $(`
                                <li class="list-group-item" id="task-${id}">
                                    <p class="mb-2 completed"></p>
                                    <small class="text-muted d-block ms-1"></small>
                                    <div class="d-flex mt-2">
                                        <button class="btn btn-sm btn-outline-info me-2 view-btn" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success me-2 complete-btn" title="Undo">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-btn">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </li>
                            `);

                            // Set text content and data attributes safely
                            $completedTask.find('p.completed').text(name);
                            $completedTask.find('small').text(
                                `Created: ${formatDate(res.created_at)} | Completed: ${formatDate(res.completed_at)}`
                            );

                            // Set data attributes for buttons
                            $completedTask.find('.view-btn')
                                .data('id', id)
                                .data('name', name)
                                .data('description', description);

                            $completedTask.find('.complete-btn, .delete-btn')
                                .data('id', id);

                            // Replace the entire task item
                            taskItem.replaceWith($completedTask);
                        } else {
                            // Create pending task structure
                            const $pendingTask = $(`
                                <li class="list-group-item" id="task-${id}">
                                    <p class="mb-2">
                                        <span class="task-text"></span>
                                    </p>
                                    <small class="text-muted d-block ms-1"></small>
                                    <div class="d-flex mt-2">
                                        <button class="btn btn-sm btn-outline-info me-2 view-btn" title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-success me-2 complete-btn" title="Complete">
                                            <i class="bi bi-check2-circle"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-btn">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </li>
                            `);

                            // Set text content and data attributes safely
                            $pendingTask.find('.task-text')
                                .text(name)
                                .data('id', id)
                                .data('name', name)
                                .data('description', description);

                            $pendingTask.find('small').text(
                                `Created: ${formatDate(res.created_at)}`);

                            // Set data attributes for buttons
                            $pendingTask.find('.view-btn')
                                .data('id', id)
                                .data('name', name)
                                .data('description', description);

                            $pendingTask.find('.complete-btn, .delete-btn')
                                .data('id', id);

                            // Replace the entire task item
                            taskItem.replaceWith($pendingTask);
                        }

                        // Update task counters
                        updateTaskCounters();
                    }
                }).fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Operation Failed',
                        text: 'Failed to update task status. Please try again.',
                        showConfirmButton: true
                    });
                });
            });

            // Delete
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Delete this task?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.post(`tasks/${id}/delete`, {
                            _token: token
                        }, function(res) {
                            if (res.success) {
                                $(`#task-${id}`).remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Update task counters
                                updateTaskCounters();
                            }
                        });
                    }
                });
            });

            // Logout
            $('#logoutBtn').on('click', function() {
                Swal.fire({
                    title: 'Logout?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, logout'
                }).then(result => {
                    if (result.isConfirmed) $('#logoutForm').submit();
                });
            });

            // Modal cleanup handlers
            $('#addTaskModal').on('hidden.bs.modal', function() {
                $('#taskName').val('');
                const addTinyMCEInstance = tinymce.get('taskDescription');
                if (addTinyMCEInstance) {
                    addTinyMCEInstance.setContent('');
                } else {
                    $('#taskDescription').val('');
                }
            });

            $('#editTaskModal').on('hidden.bs.modal', function() {
                $('#editTaskId').val('');
                $('#editTaskName').val('');
                const editTinyMCEInstance = tinymce.get('editTaskDescription');
                if (editTinyMCEInstance) {
                    editTinyMCEInstance.setContent('');
                } else {
                    $('#editTaskDescription').val('');
                }
            });

            // Fix accessibility issues with modals
            $('#addTaskModal, #editTaskModal, #viewTaskModal').on('show.bs.modal', function() {
                // Remove aria-hidden when modal is showing
                $(this).removeAttr('aria-hidden');
            }).on('shown.bs.modal', function() {
                // Ensure proper focus management
                const firstInput = $(this).find('input, textarea, button').not('[data-bs-dismiss]').first();
                if (firstInput.length) {
                    firstInput.focus();
                }
            }).on('hide.bs.modal', function() {
                // Ensure TinyMCE editors are properly saved before hiding
                $(this).find('textarea').each(function() {
                    const editor = tinymce.get(this.id);
                    if (editor) {
                        editor.save();
                    }
                });
            });
        });
    </script>

</body>

</html>
