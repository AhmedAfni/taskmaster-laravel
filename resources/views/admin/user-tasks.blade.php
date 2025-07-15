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
                        <th>Completed At</th>
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
                                @if ($task->completed && $task->completed_at)
                                    {{ \Carbon\Carbon::parse($task->completed_at)->format('d M Y, h:i A') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <!-- View -->
                                <button type="button" class="btn btn-outline-info btn-sm" title="View Details"
                                    data-bs-toggle="modal" data-bs-target="#viewTaskModal{{ $task->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>

                                <!-- Complete / Undo -->
                                <form
                                    action="{{ $task->completed ? route('admin.tasks.undo', $task->id) : route('admin.tasks.complete', $task->id) }}"
                                    method="POST" class="d-inline complete-undo-form">
                                    @csrf
                                    <button type="button"
                                        class="btn btn-outline-{{ $task->completed ? 'warning' : 'success' }} btn-sm complete-undo-btn"
                                        title="{{ $task->completed ? 'Mark as Incomplete' : 'Mark as Complete' }}"
                                        data-task-id="{{ $task->id }}"
                                        data-action="{{ $task->completed ? 'undo' : 'complete' }}">
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
                                    <div class="modal-dialog modal-xl">
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
                                                    <div class="border rounded p-3 bg-light" style="line-height: 1.5;">
                                                        {{ $task->name }}
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Description</label>
                                                    <div class="border rounded p-3 bg-light"
                                                        style="min-height: 100px; max-height: 500px; overflow-y: auto; white-space: normal; line-height: 1.5; word-wrap: break-word;">
                                                        {!! $task->description ?? 'No description provided' !!}
                                                    </div>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label fw-bold">Additional Description</label>
                                                    <div class="border rounded p-3 bg-light"
                                                        style="min-height: 50px; max-height: 300px; overflow-y: auto; white-space: pre-wrap; line-height: 1.5; word-wrap: break-word;">
                                                        {!! $task->description2 ?? '<span class="text-muted">No additional description</span>' !!}
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
                                    <div class="modal-dialog modal-xl">
                                        <form method="POST" action="{{ route('admin.tasks.edit', $task->id) }}"
                                            novalidate>
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
                                                            value="{{ $task->name }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Description</label>
                                                        <textarea name="description" id="editTaskDescription{{ $task->id }}" class="form-control tinymce-editor"
                                                            rows="4" placeholder="Enter task description...">{{ $task->description }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Additional
                                                            Description</label>
                                                        <textarea name="description2" id="editTaskDescription2{{ $task->id }}" class="form-control ckeditor-editor"
                                                            rows="3" placeholder="Enter additional description (optional)...">{{ $task->description2 }}</textarea>
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

    <!-- Image Zoom Modal -->
    <div class="image-zoom-modal" id="imageZoomModal">
        <img src="" alt="Zoomed Image" id="zoomedImage">
    </div>
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

        /* Rich text content styling for view modal */
        .modal-body .bg-light {
            font-family: Arial, sans-serif;
        }

        .modal-body .bg-light h1,
        .modal-body .bg-light h2,
        .modal-body .bg-light h3 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        .modal-body .bg-light p {
            margin-bottom: 0.5rem;
        }

        .modal-body .bg-light ul,
        .modal-body .bg-light ol {
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
        }

        .modal-body .bg-light strong {
            font-weight: bold;
        }

        .modal-body .bg-light em {
            font-style: italic;
        }

        .modal-body .bg-light img {
            max-width: 100%;
            height: auto;
            border-radius: 0.375rem;
            margin: 0.5rem 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .modal-body .bg-light img:hover {
            transform: scale(1.02);
        }

        /* TinyMCE container styling */
        .tox-tinymce {
            border-radius: 0.375rem !important;
        }

        /* Fix for TinyMCE validation issues */
        textarea[aria-hidden="true"] {
            position: absolute !important;
            left: -9999px !important;
            opacity: 0 !important;
            pointer-events: none !important;
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

        /* Style for CKEditor5 tables in view modal */
        .modal-body .bg-light table {
            border-collapse: collapse;
            width: 100%;
            margin: 15px 0;
        }

        .modal-body .bg-light table td,
        .modal-body .bg-light table th {
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            text-align: left;
        }

        .modal-body .bg-light table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .modal-body .bg-light table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .modal-body .bg-light table tbody tr:hover {
            background-color: #e9ecef;
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

        /* Table row fade effect */
        .table tbody tr {
            transition: opacity 0.3s ease;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/96s1bjh0dbr79aoe5h20vpcele61qmaimpdu7rgotiln64xm/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <!-- CKEditor5 -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

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
            }); // Initialize TinyMCE for first description fields in edit modals
            tinymce.init({
                selector: 'textarea.tinymce-editor',
                height: 300,
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | removeformat | help',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                images_upload_url: '{{ route('admin.upload.image') }}',
                images_upload_handler: function(blobInfo, success, failure) {
                    var xhr, formData;
                    xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', '{{ route('admin.upload.image') }}');
                    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector(
                        'meta[name="csrf-token"]').getAttribute('content'));
                    xhr.onload = function() {
                        var json;
                        if (xhr.status != 200) {
                            failure('HTTP Error: ' + xhr.status);
                            return;
                        }
                        json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.url != 'string') {
                            failure('Invalid JSON: ' + xhr.responseText);
                            return;
                        }
                        success(json.url);
                    };
                    formData = new FormData();
                    formData.append('upload', blobInfo.blob(), blobInfo.filename());
                    xhr.send(formData);
                },
                setup: function(editor) {
                    // Remove aria-hidden when editor is ready to prevent focus issues
                    editor.on('init', function() {
                        const element = editor.getElement();
                        if (element) {
                            element.removeAttribute('aria-hidden');
                            element.setAttribute('tabindex', '-1');
                            // Override browser validation to prevent focus issues
                            element.setCustomValidity = function() {};
                            console.log('TinyMCE editor initialized for:', element.id);
                        }
                    });

                    // Handle focus events properly
                    editor.on('focus', function() {
                        const element = editor.getElement();
                        if (element) {
                            element.removeAttribute('aria-hidden');
                        }
                    });

                    // Save content to textarea on change
                    editor.on('change', function() {
                        editor.save();
                    });
                }
            });

            // Initialize CKEditor5 for second description fields in edit modals only
            $("textarea.ckeditor-editor").each(function() {
                const element = this;
                if (!element._ckeditorInstance) {
                    ClassicEditor.create(element, {
                        toolbar: [
                            'heading', '|', 'bold', 'italic', '|', 'numberedList',
                            'bulletedList', '|', 'outdent', 'indent', '|', 'link',
                            'blockQuote', 'insertTable', '|', 'undo', 'redo'
                        ],
                        placeholder: 'Enter additional description...'
                    }).then(editor => {
                        element._ckeditorInstance = editor;
                    }).catch(error => {
                        console.error('CKEditor5 initialization error:', error);
                    });
                }
            });

            // Prevent browser validation issues with TinyMCE hidden textareas
            $(document).on('invalid', 'textarea.tinymce-editor', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const textarea = this;
                const editor = tinymce.get(textarea.id);

                if (editor) {
                    // Focus the TinyMCE editor instead
                    editor.focus();

                    // Show validation message
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please fill in the description field',
                        confirmButtonColor: '#ffc107'
                    });
                }

                return false;
            });

            // Handle complete/undo button clicks directly
            $(document).on('click', '.complete-undo-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const btn = $(this);
                const form = btn.closest('.complete-undo-form');
                const taskId = btn.data('task-id');
                const action = btn.data('action');
                const taskRow = btn.closest('tr');

                console.log(`Button clicked: Task ${taskId}, Action: ${action}`);

                // Show loading state
                btn.prop('disabled', true);
                const originalHtml = btn.html();
                btn.html('<i class="bi bi-spinner spinner-border spinner-border-sm"></i>');

                // Get CSRF token
                const csrfToken = form.find('input[name="_token"]').val();

                // Determine URL
                const url = action === 'complete' ?
                    `{{ url('admin/tasks') }}/${taskId}/complete` :
                    `{{ url('admin/tasks') }}/${taskId}/undo`;

                console.log(`Making AJAX request to: ${url}`);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        console.log('AJAX Success:', response);

                        const actionText = action === 'complete' ? 'completed' :
                            'marked as pending';

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: `Task ${actionText} successfully`,
                            confirmButtonColor: '#198754',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Update the task row
                        updateTaskRowAfterCompletion(taskRow, response.task || {}, action ===
                            'complete');
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', xhr, status, error);
                        console.error('Response Text:', xhr.responseText);

                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 419) {
                            errorMessage = 'Session expired. Please refresh the page.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error occurred. Please try again.';
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
                        btn.prop('disabled', false).html(originalHtml);
                    }
                });

                return false;
            });

            // Handle edit form submission with AJAX - more specific selector
            $(document).on('submit', 'form[action*="/admin/tasks/"][action*="/edit"]', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Edit form submitted - event captured');
                console.log('Form element:', this);
                console.log('Form action:', $(this).attr('action'));
                console.log('Form method:', $(this).attr('method'));

                handleEditFormSubmission($(this));

                return false;
            });

            // Alternative: Also handle by button click inside edit modal
            $(document).on('click', 'button[type="submit"]', function(e) {
                const form = $(this).closest('form');
                const action = form.attr('action');

                // Only handle admin task edit forms
                if (action && action.includes('/admin/tasks/') && action.includes('/edit')) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('Edit button clicked - handling form submission');
                    handleEditFormSubmission(form);

                    return false;
                }
            });

            // Handle delete form submission with AJAX
            $(document).on('submit', '.delete-task-form', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('Delete form submitted');
                const form = $(this);
                const taskRow = form.closest('tr');

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
                        // AJAX deletion
                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: form.serialize(),
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Task has been deleted successfully',
                                    confirmButtonColor: '#198754',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Remove row from table
                                if ($.fn.DataTable.isDataTable('#tasksTable')) {
                                    $('#tasksTable').DataTable().row(taskRow).remove()
                                        .draw();
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

                return false;
            });

            // Function to handle edit form submission with AJAX
            function handleEditFormSubmission(form) {
                console.log('Starting edit form submission...', form);

                // Save TinyMCE editors content first
                if (typeof tinymce !== 'undefined') {
                    tinymce.triggerSave();
                    form.find('textarea.tinymce-editor').each(function() {
                        const editor = tinymce.get(this.id);
                        if (editor) {
                            editor.save();
                            console.log('Saved TinyMCE editor:', this.id);
                        }
                    });
                }

                // Save all CKEditor5 editors before form submission
                $("textarea.ckeditor-editor").each(function() {
                    if (this._ckeditorInstance) {
                        this.value = this._ckeditorInstance.getData();
                    }
                });

                // Get form data
                const taskName = form.find('input[name="name"]').val().trim();

                // Get description - simplified
                let taskDescription = '';
                const descriptionTextarea = form.find('textarea[name="description"]')[0];
                if (descriptionTextarea) {
                    const editor = tinymce.get(descriptionTextarea.id);
                    if (editor) {
                        taskDescription = editor.getContent();
                    } else {
                        taskDescription = $(descriptionTextarea).val();
                    }
                    taskDescription = taskDescription ? taskDescription.trim() : '';
                }

                console.log('Data:', {
                    taskName,
                    descriptionLength: taskDescription.length
                });

                // Simple validation
                if (!taskName) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Task name is required',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                if (!taskDescription) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Task description is required',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                console.log('Validation passed - proceeding with submission');

                const formData = new FormData(form[0]);
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();
                const modal = form.closest('.modal');
                const url = form.attr('action');

                console.log('Form action URL:', url);

                // Ensure CSRF token is present
                if (!formData.has('_token')) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (csrfToken) {
                        formData.append('_token', csrfToken.getAttribute('content'));
                    }
                }

                // Show loading state
                submitBtn.prop('disabled', true).html(
                    '<i class="bi bi-spinner spinner-border spinner-border-sm me-1"></i> Updating...'
                );

                $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .done(function(response) {
                        console.log('Edit success response:', response);

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Task updated successfully',
                            confirmButtonColor: '#198754',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Close modal
                        modal.modal('hide');

                        // Reload the page to show updated data
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    })
                    .fail(function(xhr) {
                        console.error('Edit error:', xhr);

                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join(', ');
                        } else if (xhr.status === 419) {
                            errorMessage = 'Session expired. Please refresh the page.';
                        } else if (xhr.status === 422) {
                            errorMessage = 'Validation failed. Please check your input.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error occurred. Please try again.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                            confirmButtonColor: '#dc3545'
                        });
                    })
                    .always(function() {
                        // Restore button
                        submitBtn.prop('disabled', false).html(originalText);
                    });
            }

            // Function to update task row after completion/undo
            function updateTaskRowAfterCompletion(taskRow, task, isCompleting) {
                const statusCell = taskRow.find('td:nth-child(3)');
                const completedCell = taskRow.find('td:nth-child(5)');
                const actionButtons = taskRow.find('td:nth-child(6)');

                // Update status badge
                if (isCompleting) {
                    statusCell.html('<span class="badge bg-success">Completed</span>');
                } else {
                    statusCell.html('<span class="badge bg-warning text-dark">Pending</span>');
                }

                // Update completed date
                if (isCompleting && task.completed_at) {
                    const date = new Date(task.completed_at);
                    const formattedDate = date.toLocaleDateString('en-US', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    });
                    completedCell.html(formattedDate);
                } else {
                    completedCell.html('<span class="text-muted">-</span>');
                }

                // Update complete/undo button
                const completeBtn = actionButtons.find('.complete-undo-btn');

                if (isCompleting) {
                    // Update button styling and text - use warning color for completed tasks (undo button)
                    completeBtn.removeClass('btn-outline-success').addClass('btn-outline-warning');
                    completeBtn.attr('title', 'Mark as Incomplete');
                    completeBtn.attr('data-action', 'undo');
                    completeBtn.find('i').removeClass('bi-check-circle').addClass('bi-check-circle-fill');
                } else {
                    // Update button styling and text - use success color for pending tasks (complete button)
                    completeBtn.removeClass('btn-outline-warning').addClass('btn-outline-success');
                    completeBtn.attr('title', 'Mark as Complete');
                    completeBtn.attr('data-action', 'complete');
                    completeBtn.find('i').removeClass('bi-check-circle-fill').addClass('bi-check-circle');
                }
            }

            // Image zoom functionality for view modals
            $(document).on('click', '.modal-body .bg-light img', function(e) {
                e.stopPropagation();
                const imgSrc = $(this).attr('src');
                $('#zoomedImage').attr('src', imgSrc);
                $('#imageZoomModal').fadeIn(200);
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
