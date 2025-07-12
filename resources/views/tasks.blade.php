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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tiny.cloud/1/96s1bjh0dbr79aoe5h20vpcele61qmaimpdu7rgotiln64xm/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>

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

            // Initialize TinyMCE for Add Task Modal
            tinymce.init({
                selector: '#taskDescription',
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

                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const img = new Image();
                                    img.onload = function() {
                                        const canvas = document.createElement('canvas');
                                        const ctx = canvas.getContext('2d');

                                        // Resize image if too large (increased limits)
                                        let {
                                            width,
                                            height
                                        } = img;
                                        const maxWidth = 1200;
                                        const maxHeight = 900;

                                        if (width > maxWidth || height > maxHeight) {
                                            const ratio = Math.min(maxWidth / width,
                                                maxHeight / height);
                                            width *= ratio;
                                            height *= ratio;
                                        }

                                        canvas.width = width;
                                        canvas.height = height;
                                        ctx.drawImage(img, 0, 0, width, height);

                                        const compressedDataUrl = canvas.toDataURL(
                                            'image/jpeg', 0.9);
                                        callback(compressedDataUrl, {
                                            alt: file.name
                                        });
                                    };
                                    img.src = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                        input.click();
                    }
                },
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px; line-height:1.6; } img { max-width: 100%; height: auto; }',
                setup: function(editor) {
                    editor.on('change', function() {
                        editor.save();
                    });
                }
            });

            // Initialize TinyMCE for Edit Task Modal
            tinymce.init({
                selector: '#editTaskDescription',
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

                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    const img = new Image();
                                    img.onload = function() {
                                        const canvas = document.createElement('canvas');
                                        const ctx = canvas.getContext('2d');

                                        // Resize image if too large (increased limits)
                                        let {
                                            width,
                                            height
                                        } = img;
                                        const maxWidth = 1200;
                                        const maxHeight = 900;

                                        if (width > maxWidth || height > maxHeight) {
                                            const ratio = Math.min(maxWidth / width,
                                                maxHeight / height);
                                            width *= ratio;
                                            height *= ratio;
                                        }

                                        canvas.width = width;
                                        canvas.height = height;
                                        ctx.drawImage(img, 0, 0, width, height);

                                        const compressedDataUrl = canvas.toDataURL(
                                            'image/jpeg', 0.9);
                                        callback(compressedDataUrl, {
                                            alt: file.name
                                        });
                                    };
                                    img.src = e.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                        input.click();
                    }
                },
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px } img { max-width: 100%; height: auto; }',
                setup: function(editor) {
                    editor.on('change', function() {
                        editor.save();
                    });
                }
            });

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
            $('#addTaskForm').validate({
                submitHandler: function(form, e) {
                    e.preventDefault();
                    const name = $('#taskName').val().trim();
                    const description = tinymce.get('taskDescription').getContent();

                    if (!description.trim()) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Please enter a description',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        return;
                    }

                    // Check description length for server validation (increased for image support)
                    if (description.length > 16777215) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Description too long',
                            text: `Content is ${description.length} characters. Maximum allowed is 16MB. Please reduce content or use smaller images.`,
                            showConfirmButton: true
                        });
                        return;
                    }

                    $.post("{{ route('tasks.store') }}", {
                        name,
                        description,
                        _token: token
                    }, function(res) {
                        console.log('Task creation response:', res); // Debug log

                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Task Added!',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Create DOM elements properly to avoid HTML injection issues
                            const $taskItem = $(`
                                <li class="list-group-item" id="task-${res.task.id}">
                                    <p class="mb-2">
                                        <span class="task-text"></span>
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

                            // Set data attributes and text safely
                            $taskItem.find('.task-text')
                                .text(res.task.name)
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
                            tinymce.get('taskDescription').setContent('');
                            addModal.hide();

                            // Update task counters
                            updateTaskCounters();
                        }
                    }).fail(function(xhr) {
                        console.error('Error details:', xhr.responseText);
                        let errorMessage = 'Failed to add task';

                        if (xhr.status === 422) {
                            try {
                                const errors = JSON.parse(xhr.responseText);
                                if (errors.errors) {
                                    errorMessage = Object.values(errors.errors).flat().join(
                                        ', ');
                                } else if (errors.message) {
                                    errorMessage = errors.message;
                                }
                            } catch (e) {
                                errorMessage = 'Validation error - content may be too large';
                            }
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage,
                            timer: 4000,
                            showConfirmButton: true
                        });
                    });
                }
            });

            // View Task
            $(document).on('click', '.view-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description');

                console.log('View button clicked:', {
                    id,
                    name,
                    description 
                }); // Debug log

                // Check if task is completed by looking at the parent task item
                const taskItem = $(`#task-${id}`);
                const isCompleted = taskItem.find('.completed').length > 0;

                $('#viewTaskName').text(name);

                // Safely set the description content
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
                    $('#viewDescriptionInfo').html(
                        '<i class="bi bi-check-circle-fill text-success"></i> Task completed'
                    );
                } else {
                    $('#viewEditBtn').show();
                    $('#viewDescriptionInfo')
                }

                // Add image click handlers for zoom functionality
                setTimeout(function() {
                    $('#viewTaskDescription img').off('click').on('click', function(e) {
                        e.stopPropagation();
                        const imgSrc = $(this).attr('src');
                        $('#zoomedImage').attr('src', imgSrc);
                        $('#imageZoomModal').fadeIn(200);
                    });
                }, 100);

                viewModal.show();
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

            // Edit from View Modal
            $('#viewEditBtn').on('click', function() {
                // Switch to edit mode
                $('#viewTaskName').addClass('d-none');
                $('#viewTaskDescription').addClass('d-none');
                $('#editViewTaskName').removeClass('d-none').val($(this).data('name'));
                $('#editViewTaskDescription').removeClass('d-none');

                // Initialize TinyMCE for the edit textarea if not already initialized
                if (!tinymce.get('editViewTaskDescription')) {
                    tinymce.init({
                        selector: '#editViewTaskDescription',
                        height: 300,
                        menubar: false,
                        plugins: [
                            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                            'preview',
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

                                        const reader = new FileReader();
                                        reader.onload = function(e) {
                                            const img = new Image();
                                            img.onload = function() {
                                                const canvas = document
                                                    .createElement('canvas');
                                                const ctx = canvas.getContext(
                                                    '2d');

                                                // Resize image if too large (increased limits)
                                                let {
                                                    width,
                                                    height
                                                } = img;
                                                const maxWidth = 1200;
                                                const maxHeight = 900;

                                                if (width > maxWidth || height >
                                                    maxHeight) {
                                                    const ratio = Math.min(
                                                        maxWidth / width,
                                                        maxHeight / height);
                                                    width *= ratio;
                                                    height *= ratio;
                                                }

                                                canvas.width = width;
                                                canvas.height = height;
                                                ctx.drawImage(img, 0, 0, width,
                                                    height);

                                                const compressedDataUrl = canvas
                                                    .toDataURL(
                                                        'image/jpeg', 0.9);
                                                callback(compressedDataUrl, {
                                                    alt: file.name
                                                });
                                            };
                                            img.src = e.target.result;
                                        };
                                        reader.readAsDataURL(file);
                                    }
                                });
                                input.click();
                            }
                        },
                        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px } img { max-width: 100%; height: auto; }',
                        setup: function(editor) {
                            editor.on('init', function() {
                                // Set content after initialization
                                editor.setContent($('#viewEditBtn').data(
                                    'description'));
                            });
                            editor.on('change', function() {
                                editor.save();
                            });
                        }
                    });
                } else {
                    // Set TinyMCE content if already initialized
                    tinymce.get('editViewTaskDescription').setContent($(this).data('description'));
                }

                // Show/hide buttons
                $('#viewEditBtn').addClass('d-none');
                $('#cancelEditBtn').removeClass('d-none');
                $('#saveEditBtn').removeClass('d-none');
            });

            // Cancel Edit in View Modal
            $('#cancelEditBtn').on('click', function() {
                // Switch back to view mode
                $('#viewTaskName').removeClass('d-none');
                $('#viewTaskDescription').removeClass('d-none');
                $('#editViewTaskName').addClass('d-none');
                $('#editViewTaskDescription').addClass('d-none');

                // Safely remove TinyMCE instance
                const editor = tinymce.get('editViewTaskDescription');
                if (editor) {
                    editor.remove();
                }

                // Clean up any leftover TinyMCE wrapper DOM (important)
                $('#editViewTaskDescription').siblings('.tox').remove();

                // Reset textarea visibility and content
                $('#editViewTaskDescription')
                    .val('') // Optional: clear content if needed
                    .addClass('d-none') // keep hidden
                    .removeAttr('style'); // remove any leftover display styles

                // Show/hide buttons
                $('#viewEditBtn').removeClass('d-none');
                $('#cancelEditBtn').addClass('d-none');
                $('#saveEditBtn').addClass('d-none');
            });



            // Save Edit in View Modal
            $('#viewEditForm').on('submit', function(e) {
                e.preventDefault();

                const id = $('#viewEditBtn').data('id');
                const name = $('#editViewTaskName').val().trim();
                const description = tinymce.get('editViewTaskDescription') ?
                    tinymce.get('editViewTaskDescription').getContent() :
                    $('#editViewTaskDescription').val();

                if (!name || !description.trim()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Please fill in all fields',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                // Check content length for server validation limit
                if (description.length > 16777215) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Content Too Large',
                        html: `
                            <p>Description is <strong>${description.length.toLocaleString()}</strong> characters.</p>
                            <p>Maximum allowed: <strong>16,777,215</strong> characters (~16MB).</p>
                            <p>Please reduce content size or compress images further.</p>
                        `,
                        showConfirmButton: true
                    });
                    return;
                }

                // Show loading indicator for large content
                const loadingToast = Swal.fire({
                    title: 'Saving...',
                    text: 'Processing large content with images',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.post(`tasks/${id}/edit`, {
                    _token: token,
                    name,
                    description
                }, function(res) {
                    loadingToast.close();

                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Task Updated Successfully!',
                            text: 'All changes including images have been saved.',
                            timer: 2500,
                            showConfirmButton: false
                        });

                        // Update the task in the list
                        const taskItem = $(`#task-${id}`);
                        taskItem.find('.task-text').text(name).data('name', name).data(
                            'description', res.description);
                        taskItem.find('.view-btn').data('name', name).data('description', res
                            .description);

                        // Update the view modal display
                        $('#viewTaskName').text(name);
                        $('#viewTaskDescription').html(res.description);
                        $('#viewEditBtn').data('name', name).data('description', res.description);

                        // Remove TinyMCE instance to clean up
                        if (tinymce.get('editViewTaskDescription')) {
                            tinymce.remove('#editViewTaskDescription');
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

                        // Re-add image click handlers
                        setTimeout(function() {
                            $('#viewTaskDescription img').off('click').on('click', function(
                                e) {
                                e.stopPropagation();
                                const imgSrc = $(this).attr('src');
                                $('#zoomedImage').attr('src', imgSrc);
                                $('#imageZoomModal').fadeIn(200);
                            });
                        }, 100);
                    }
                }).fail(function(xhr) {
                    loadingToast.close();

                    let errorMessage = 'Failed to update task';
                    if (xhr.status === 422) {
                        try {
                            const errors = JSON.parse(xhr.responseText);
                            if (errors.errors) {
                                errorMessage = Object.values(errors.errors).flat().join(', ');
                            } else if (errors.message) {
                                errorMessage = errors.message;
                            }
                        } catch (e) {
                            errorMessage = 'Content validation error - may be too large';
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: errorMessage,
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
                    console.error('Complete/Undo error:', xhr.responseText);
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
        });
    </script>

</body>

</html>
