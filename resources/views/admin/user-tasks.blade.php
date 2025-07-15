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
                                                        <textarea name="description" id="editTaskDescription{{ $task->id }}" class="form-control tinymce-editor"
                                                            rows="4" placeholder="Enter task description...">{{ $task->description }}</textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Additional Description</label>
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
            });


            // Initialize TinyMCE for first description fields in edit modals
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

            // Handle form submission with both TinyMCE and CKEditor5 content
            $('form[method="POST"]').on('submit', function(e) {
                // Save TinyMCE editors content
                tinymce.triggerSave();

                // Save all CKEditor5 editors before form submission
                $("textarea.ckeditor-editor").each(function() {
                    if (this._ckeditorInstance) {
                        this.value = this._ckeditorInstance.getData();
                    }
                });
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
