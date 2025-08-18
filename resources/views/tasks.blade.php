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
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

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

        #jitsiMeetSideEmbed {
            position: fixed;
            top: 0;
            right: 0;
            z-index: 1055;
            background: #fff;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.12);
            border-left: 1px solid #dee2e6;
            display: none;
            transition: width 0.3s, height 0.3s;
        }

        #jitsiMeetSideEmbed .jitsi-header {
            cursor: move;
        }

        #jitsiMeetSideEmbed.jitsi-side-view {
            width: 420px;
            height: 70vh;
            top: 15vh;
            border-radius: 12px 0 0 12px;
        }

        #jitsiMeetSideEmbed.jitsi-full-view {
            width: 100vw;
            height: 100vh;
            top: 0;
            border-radius: 0;
        }

        #jitsiMeetSideEmbed .jitsi-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: move;
            user-select: none;
        }

        #jitsiMeetSideEmbed.jitsi-side-view .jitsi-header::before {
            content: "⋮⋮";
            color: #6c757d;
            font-weight: bold;
            margin-right: 8px;
            letter-spacing: 2px;
        }

        #jitsiMeetSideIframe {
            width: 100%;
            height: calc(100% - 48px);
            border: 0;
            border-radius: 0 0 12px 0;
            background: #000;
        }

        @media (max-width: 600px) {
            #jitsiMeetSideEmbed.jitsi-side-view {
                width: 100vw;
                left: 0;
                border-radius: 0;
            }
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

                @auth
                    {{-- Google OAuth integration --}}
                    @if (!Auth::user()->google_access_token)
                        <a href="{{ route('google.oauth.redirect') }}" class="btn btn-danger btn-sm ms-2">
                            <i class="bi bi-google"></i> Connect Google Account
                        </a>
                    @else
                        <span class="badge bg-success ms-2">Google Connected</span>
                    @endif
                @endauth
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
                                        data-name="{{ $task->name }}" data-description="{{ $task->description }}"
                                        data-description2="{{ $task->description2 ?? '' }}">
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
                                    data-description2="{{ $task->description2 ?? '' }}" title="View">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @if (!$task->completed)
                                    <button class="btn btn-sm btn-outline-primary me-2 edit-btn"
                                        data-id="{{ $task->id }}" data-name="{{ $task->name }}"
                                        data-description="{{ $task->description }}"
                                        data-description2="{{ $task->description2 ?? '' }}" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-outline-success me-2 complete-btn"
                                    data-id="{{ $task->id }}"
                                    title="{{ $task->completed ? 'Undo' : 'Complete' }}">
                                    <i
                                        class="bi {{ $task->completed ? 'bi-arrow-counterclockwise' : 'bi-check2-circle' }}"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-btn" data-id="{{ $task->id }}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                                @if ($task->google_event_link)
                                    <a href="{{ $task->google_event_link }}" target="_blank"
                                        class="btn btn-outline-success btn-sm ms-2">
                                        <i class="bi bi-calendar-event"></i> View in Google Calendar
                                    </a>
                                @endif

                                @if ($task->google_meet_link)
                                    <a href="{{ $task->google_meet_link }}"
                                        class="btn btn-success btn-sm ms-2 join-google-meet-link">
                                        Join Google Meet
                                    </a>
                                @endif

                                @if ($task->jitsi_meeting_link)
                                    <button type="button" class="btn btn-warning btn-sm ms-2 join-jitsi-meet-link"
                                        data-jitsi-link="{{ $task->jitsi_meeting_link }}">
                                        Join Jitsi Meeting
                                    </button>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>

                <!-- Product List -->
                <div class="mt-5">
                    <h4 class="mb-3 text-center">My Products</h4>
                    <ul class="list-group">
                        @forelse($products as $product)
                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image"
                                            style="width:60px;height:60px;object-fit:cover;border-radius:8px;margin-right:16px;">
                                    @endif
                                    <div>
                                        <div><strong>Price:</strong> ${{ $product->price }}</div>
                                        <div><strong>Size:</strong> {{ $product->size }}</div>
                                    </div>
                                </div>
                                <div>
                                    <!-- Pay Button -->
                                    <a href="{{ route('products.pay', $product->id) }}"
                                        class="btn btn-success btn-sm me-2">
                                        <i class="bi bi-credit-card"></i> Pay
                                    </a>
                                    <!-- Delete Button -->
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">No products found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <script>
                $(document).on('click', '.join-google-meet-link', function(e) {
                    e.preventDefault();
                    const url = $(this).attr('href');
                    window.open(url, 'GoogleMeetPopup', 'width=900,height=700,menubar=no,toolbar=no,location=no,status=no');
                });
                // Jitsi Meet embed in right-side panel logic
                $(document).on('click', '.join-jitsi-meet-link', function(e) {
                    e.preventDefault();
                    const jitsiUrl = $(this).data('jitsi-link');
                    if (!jitsiUrl) return;
                    // Show the Jitsi panel in minimized (side) mode
                    $('#jitsiMeetSideEmbed').show();
                    $('#jitsiMeetSideEmbed').removeClass('jitsi-full-view').addClass('jitsi-side-view');
                    $('#jitsiMeetSideIframe').attr('src', jitsiUrl +
                        '#userInfo.displayName={{ Auth::user()->name ?? '' }}');
                    // Optionally, dim or disable main content interaction
                });
                // Toggle to full view
                $(document).on('click', '#expandJitsiSideEmbed', function() {
                    $('#jitsiMeetSideEmbed').removeClass('jitsi-side-view').addClass('jitsi-full-view');
                });
                // Toggle back to side view
                $(document).on('click', '#minimizeJitsiSideEmbed', function() {
                    $('#jitsiMeetSideEmbed').removeClass('jitsi-full-view').addClass('jitsi-side-view');
                });
                // Close Jitsi panel
                $(document).on('click', '#closeJitsiSideEmbed', function() {
                    $('#jitsiMeetSideEmbed').hide();
                    $('#jitsiMeetSideIframe').attr('src', '');
                });
            </script>

            <div id="jitsiMeetSideEmbed" class="jitsi-side-view" style="display:none;">
                <div class="jitsi-header">
                    <span><strong>Jitsi Meeting</strong></span>
                    <div>
                        <button id="expandJitsiSideEmbed" class="btn btn-sm btn-outline-primary me-1"
                            title="Full View"><i class="bi bi-arrows-fullscreen"></i></button>
                        <button id="minimizeJitsiSideEmbed" class="btn btn-sm btn-outline-secondary me-1"
                            title="Minimize" style="display:none;"><i class="bi bi-arrow-bar-right"></i></button>
                        <button id="closeJitsiSideEmbed" class="btn btn-sm btn-outline-danger"
                            title="Close">&times;</button>
                    </div>
                </div>
                <iframe id="jitsiMeetSideIframe" src=""
                    allow="camera; microphone; fullscreen; display-capture"></iframe>
            </div>
            <script>
                // Show/hide expand/minimize buttons based on view
                function updateJitsiButtons() {
                    if ($('#jitsiMeetSideEmbed').hasClass('jitsi-full-view')) {
                        $('#expandJitsiSideEmbed').hide();
                        $('#minimizeJitsiSideEmbed').show();
                    } else {
                        $('#expandJitsiSideEmbed').show();
                        $('#minimizeJitsiSideEmbed').hide();
                    }
                }
                $(document).on('click', '#expandJitsiSideEmbed, #minimizeJitsiSideEmbed', updateJitsiButtons);
                $(document).on('click', '.join-jitsi-meet-link', updateJitsiButtons);
                // Also update on close
                $(document).on('click', '#closeJitsiSideEmbed', function() {
                    $('#expandJitsiSideEmbed').show();
                    $('#minimizeJitsiSideEmbed').hide();
                });

                // Drag and drop functionality for Jitsi window
                let isDragging = false;
                let dragStartX, dragStartY, startLeft, startTop;

                $(document).on('mousedown', '#jitsiMeetSideEmbed .jitsi-header', function(e) {
                    // Only allow dragging in side view mode, not full view
                    if ($('#jitsiMeetSideEmbed').hasClass('jitsi-full-view')) {
                        return;
                    }

                    // Don't drag if clicking on buttons
                    if ($(e.target).is('button') || $(e.target).is('i')) {
                        return;
                    }

                    isDragging = true;
                    const $jitsiWindow = $('#jitsiMeetSideEmbed');

                    dragStartX = e.clientX;
                    dragStartY = e.clientY;
                    startLeft = parseInt($jitsiWindow.css('left')) || (window.innerWidth - $jitsiWindow.outerWidth());
                    startTop = parseInt($jitsiWindow.css('top')) || 0;

                    $jitsiWindow.css('transition', 'none');
                    $(document.body).css('user-select', 'none');
                    e.preventDefault();
                });

                $(document).on('mousemove', function(e) {
                    if (!isDragging) return;

                    const $jitsiWindow = $('#jitsiMeetSideEmbed');
                    const deltaX = e.clientX - dragStartX;
                    const deltaY = e.clientY - dragStartY;

                    let newLeft = startLeft + deltaX;
                    let newTop = startTop + deltaY;

                    // Constrain to viewport
                    const windowWidth = $jitsiWindow.outerWidth();
                    const windowHeight = $jitsiWindow.outerHeight();
                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;

                    newLeft = Math.max(0, Math.min(newLeft, viewportWidth - windowWidth));
                    newTop = Math.max(0, Math.min(newTop, viewportHeight - windowHeight));

                    $jitsiWindow.css({
                        left: newLeft + 'px',
                        top: newTop + 'px',
                        right: 'auto'
                    });
                });

                $(document).on('mouseup', function() {
                    if (isDragging) {
                        isDragging = false;
                        $('#jitsiMeetSideEmbed').css('transition', 'width 0.3s, height 0.3s');
                        $(document.body).css('user-select', '');
                    }
                });

                // Reset position when toggling between views
                $(document).on('click', '#expandJitsiSideEmbed', function() {
                    $('#jitsiMeetSideEmbed').css({
                        left: '',
                        top: '',
                        right: '0'
                    });
                });

                $(document).on('click', '#minimizeJitsiSideEmbed', function() {
                    $('#jitsiMeetSideEmbed').css({
                        left: '',
                        top: '15vh',
                        right: '0'
                    });
                });
            </script>
        </div>
    </div>
    </div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <form id="addTaskForm" class="modal-content" novalidate>
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
                            placeholder="Enter task description..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="taskDescription2" class="form-label">Additional Description</label>
                        <textarea class="form-control" id="taskDescription2" name="description2" rows="3"
                            placeholder="Enter additional description (optional)..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="taskImage" class="form-label">Task Image</label>
                        <input type="file" class="form-control" id="taskImage" name="images[]" accept="image/*"
                            multiple>
                    </div>
                    <div class="mb-3">
                        <label for="scheduledAt" class="form-label">Schedule Meeting Date & Time (Google Meet)</label>
                        <input type="datetime-local" class="form-control" id="scheduledAt" name="scheduled_at">
                    </div>
                    <div class="mb-3">
                        <label for="jitsiScheduledAt" class="form-label">Schedule Jitsi Meeting Date & Time</label>
                        <input type="datetime-local" class="form-control" id="jitsiScheduledAt"
                            name="jitsi_scheduled_at">
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
            <form id="modalEditForm" class="modal-content" novalidate>
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
                            placeholder="Enter task description..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editTaskDescription2" class="form-label">Additional Description</label>
                        <textarea class="form-control" id="editTaskDescription2" name="editTaskDescription2" rows="3"
                            placeholder="Enter additional description (optional)..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editScheduledAt" class="form-label">Schedule Meeting Date & Time</label>
                        <input type="datetime-local" class="form-control" id="editScheduledAt"
                            name="editScheduledAt">
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
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Task Heading</label>
                        <p class="form-control-plaintext border rounded p-2 bg-light" id="viewTaskName"></p>
                    </div>
                    <div class="mb-3" id="viewTaskScheduledContainer" style="display:none;">
                        <label class="form-label fw-bold">Scheduled Meeting</label>
                        <div class="border rounded p-2 bg-light" id="viewTaskScheduled"></div>
                    </div>
                    <div class="mb-3" id="viewTaskImagesContainer" style="display:none;">
                        <label class="form-label fw-bold">Images</label>
                        <div id="viewTaskImages"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <div class="border rounded p-3 bg-light" id="viewTaskDescription"
                            style="min-height: 100px; max-height: 500px; overflow-y: auto; white-space: normal; line-height: 1.5; word-wrap: break-word;">
                        </div>
                    </div>
                    <div class="mb-3">
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
                    <a id="fullTaskViewBtn" href="#" target="_blank" class="btn btn-primary">
                        <i class="bi bi-box-arrow-up-right"></i> Full Task View
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Zoom Modal -->
    <div class="image-zoom-modal" id="imageZoomModal">
        <img src="" alt="Zoomed Image" id="zoomedImage">
    </div>

    <!-- JS -->
    <style>
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

        /* Fix for TinyMCE validation issues */
        textarea[aria-hidden="true"] {
            position: absolute !important;
            left: -9999px !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }
    </style>
    <script>
        const BASE_URL = "{{ url('/') }}";
    </script>
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
                    relative_urls: false,
                    remove_script_host: false,
                    convert_urls: false,
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
            } else {
                console.warn('TinyMCE is not loaded. Rich text editing will not be available.');
            }

            // Initialize CKEditor5 for second description fields
            let addTaskEditor2;
            let editTaskEditor2;

            // Simple CKEditor5 initialization when Add Task modal is shown
            $('#addTaskModal').on('shown.bs.modal', function() {
                console.log('Add Task modal shown, checking CKEditor5...');
                // Only initialize if not already done
                if (!addTaskEditor2) {
                    const element = document.querySelector('#taskDescription2');
                    console.log('Element found:', element);
                    console.log('ClassicEditor available:', typeof ClassicEditor !== 'undefined');

                    if (element && typeof ClassicEditor !== 'undefined') {
                        console.log('Initializing CKEditor5...');
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
                                addTaskEditor2 = editor;
                                console.log('CKEditor5 initialized successfully');
                            })
                            .catch(error => {
                                console.error('CKEditor5 initialization error:', error);
                            });
                    } else {
                        console.error('CKEditor5 element not found or ClassicEditor not available');
                        console.log('Element:', element);
                        console.log('ClassicEditor type:', typeof ClassicEditor);

                        // Try again after a short delay in case the script is still loading
                        if (element && typeof ClassicEditor === 'undefined') {
                            console.log('Retrying CKEditor5 initialization after delay...');
                            setTimeout(() => {
                                if (typeof ClassicEditor !== 'undefined') {
                                    console.log('CKEditor5 now available, initializing...');
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
                                            addTaskEditor2 = editor;
                                            console.log(
                                                'CKEditor5 initialized successfully (delayed)'
                                            );
                                        })
                                        .catch(error => {
                                            console.error(
                                                'CKEditor5 delayed initialization error:',
                                                error);
                                        });
                                } else {
                                    console.error('CKEditor5 still not available after delay');
                                }
                            }, 1000);
                        }
                    }
                } else {
                    console.log('CKEditor5 already initialized');
                }
            });

            // Clean up CKEditor5 when modal is hidden
            $('#addTaskModal').on('hidden.bs.modal', function() {
                if (addTaskEditor2) {
                    addTaskEditor2.destroy().then(() => {
                        addTaskEditor2 = null;
                    });
                }
            });

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

            function formatDate(dateStr) {
                const d = new Date(dateStr);
                return d.toLocaleString();
            }

            // Function to fix image URLs in content
            function fixImageUrls(content) {
                if (!content) return content;

                const baseUrl = '{{ config('app.url') }}';

                // Replace relative URLs starting with ../storage/ with absolute URLs
                content = content.replace(/src=["']\.\.\/storage\//g, `src="${baseUrl}/storage/`);

                // Replace relative URLs starting with storage/ with absolute URLs
                content = content.replace(/src=["'](?!https?:\/\/)storage\//g, `src="${baseUrl}/storage/`);

                return content;
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

            // Prevent browser validation issues with TinyMCE hidden textareas
            $(document).on('invalid', 'textarea', function(e) {
                const textarea = this;

                // Check if this is a TinyMCE textarea
                if (textarea.hasAttribute('aria-hidden') || $(textarea).css('display') === 'none') {
                    e.preventDefault();
                    e.stopPropagation();

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
                }
            });

            // Add Task
            $('#addTaskForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                const name = $('#taskName').val().trim();

                // Get description from TinyMCE if available, otherwise from textarea
                let description = '';
                const tinyMCEInstance = tinymce.get('taskDescription');
                if (tinyMCEInstance && typeof tinyMCEInstance.getContent === 'function') {
                    try {
                        tinyMCEInstance.save();
                        description = tinyMCEInstance.getContent().trim();
                    } catch (error) {
                        console.error('Error getting TinyMCE content:', error);
                        description = $('#taskDescription').val().trim();
                    }
                } else {
                    description = $('#taskDescription').val().trim();
                }

                // Get second description from CKEditor5 if available, otherwise from textarea
                let description2 = '';
                if (addTaskEditor2 && typeof addTaskEditor2.getData === 'function') {
                    try {
                        description2 = addTaskEditor2.getData().trim();
                    } catch (error) {
                        console.error('Error getting CKEditor5 content:', error);
                        description2 = $('#taskDescription2').val().trim();
                    }
                } else {
                    description2 = $('#taskDescription2').val().trim();
                }

                // Get scheduled_at value
                const scheduledAt = $('#scheduledAt').val();
                // Get jitsi_scheduled_at value
                const jitsiScheduledAt = $('#jitsiScheduledAt').val();

                // Prepare FormData for AJAX
                const formData = new FormData();
                formData.append('name', name);
                formData.append('description', description);
                formData.append('description2', description2);
                formData.append('scheduled_at', scheduledAt);
                formData.append('jitsi_scheduled_at', jitsiScheduledAt);
                formData.append('_token', token);

                // Append image if selected
                const imageFiles = $('#taskImage')[0].files;
                if (imageFiles.length > 0) {
                    for (let i = 0; i < imageFiles.length; i++) {
                        formData.append('images[]', imageFiles[i]);
                    }
                }

                $.ajax({
                    url: "{{ route('tasks.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Task Added!',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            location.reload();
                        }
                    },
                    error: function(xhr) {
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
                            } catch (e) {}
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Adding Task',
                            text: errorMessage,
                            timer: 5000,
                            showConfirmButton: true
                        });
                    }
                });
            });

            // Edit Task Modal functionality - Click on task text to edit (quick edit)
            $(document).on('click', '.task-text', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description');
                const description2 = $(this).data('description2') || '';

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

                // Fetch complete task data from API to get scheduled_at
                $.get(BASE_URL + '/api/tasks/' + id, function(res) {
                    if (res.success && res.task) {
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

                        // Set additional description in CKEditor5 if available, otherwise in textarea
                        const editDesc2Element = document.querySelector('#editTaskDescription2');
                        if (editTaskEditor2) {
                            editTaskEditor2.setData(description2);
                        } else {
                            // Store the data temporarily for when the editor is initialized
                            $(editDesc2Element).data('pending-content', description2);
                            $('#editTaskDescription2').val(description2);
                        }

                        // Set scheduled_at if available
                        if (res.task.scheduled_at) {
                            // Convert to datetime-local format (YYYY-MM-DDTHH:MM)
                            const scheduledDate = new Date(res.task.scheduled_at);
                            const formattedDate = scheduledDate.toISOString().slice(0, 16);
                            $('#editScheduledAt').val(formattedDate);
                        } else {
                            $('#editScheduledAt').val('');
                        }

                        editModal.show();
                    }
                });
            });

            // Edit Task Modal functionality - Click on edit button (explicit edit)
            $(document).on('click', '.edit-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const description = $(this).data('description');
                const description2 = $(this).data('description2') || '';

                // Fetch complete task data from API to get scheduled_at
                $.get(BASE_URL + '/api/tasks/' + id, function(res) {
                    if (res.success && res.task) {
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

                        // Set additional description in CKEditor5 if available, otherwise in textarea
                        const editDesc2Element = document.querySelector('#editTaskDescription2');
                        if (editTaskEditor2) {
                            editTaskEditor2.setData(description2);
                        } else {
                            // Store the data temporarily for when the editor is initialized
                            $(editDesc2Element).data('pending-content', description2);
                            $('#editTaskDescription2').val(description2);
                        }

                        // Set scheduled_at if available
                        if (res.task.scheduled_at) {
                            // Convert to datetime-local format (YYYY-MM-DDTHH:MM)
                            const scheduledDate = new Date(res.task.scheduled_at);
                            const formattedDate = scheduledDate.toISOString().slice(0, 16);
                            $('#editScheduledAt').val(formattedDate);
                        } else {
                            $('#editScheduledAt').val('');
                        }

                        editModal.show();
                    }
                });
            });

            // Edit Task Form Submission
            $('#modalEditForm').on('submit', function(e) {
                e.preventDefault();

                console.log('Edit form submitted');

                const id = $('#editTaskId').val();
                const name = $('#editTaskName').val().trim();

                // Get description from TinyMCE if available, otherwise from textarea
                let description = '';
                const editTinyMCEInstance = tinymce.get('editTaskDescription');
                if (editTinyMCEInstance && typeof editTinyMCEInstance.getContent === 'function') {
                    try {
                        editTinyMCEInstance.save();
                        description = editTinyMCEInstance.getContent().trim();
                    } catch (error) {
                        console.error('Error getting TinyMCE content:', error);
                        description = $('#editTaskDescription').val().trim();
                    }
                } else {
                    description = $('#editTaskDescription').val().trim();
                }

                // Get second description from CKEditor5 if available, otherwise from textarea
                let description2 = '';
                if (editTaskEditor2 && typeof editTaskEditor2.getData === 'function') {
                    try {
                        description2 = editTaskEditor2.getData().trim();
                    } catch (error) {
                        console.error('Error getting CKEditor5 content:', error);
                        description2 = $('#editTaskDescription2').val().trim();
                    }
                } else {
                    description2 = $('#editTaskDescription2').val().trim();
                }

                // Get scheduled_at value
                const scheduledAt = $('#editScheduledAt').val();

                console.log('Validation passed, submitting...');

                // Submit the form
                $.post(`tasks/${id}/edit`, {
                        _token: token,
                        name: name,
                        description: description,
                        description2: description2,
                        scheduled_at: scheduledAt
                    })
                    .done(function(res) {
                        console.log('Success response:', res);
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Task Updated!',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Update the task in the list
                            const taskItem = $(`#task-${id}`);
                            taskItem.find('.task-text').text(name)
                                .data('name', name)
                                .data('description', description)
                                .data('description2', description2);

                            taskItem.find('.view-btn')
                                .data('name', name)
                                .data('description', description)
                                .data('description2', description2);

                            taskItem.find('.edit-btn')
                                .data('name', name)
                                .data('description', description)
                                .data('description2', description2);

                            // Close modal
                            editModal.hide();

                            // Clear form
                            $('#editTaskId').val('');
                            $('#editTaskName').val('');
                            if (editTinyMCEInstance) {
                                editTinyMCEInstance.setContent('');
                            } else {
                                $('#editTaskDescription').val('');
                            }
                            if (editTaskEditor2) {
                                editTaskEditor2.setData('');
                            } else {
                                $('#editTaskDescription2').val('');
                            }
                            $('#editScheduledAt').val('');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: res.message || 'Unknown error occurred'
                            });
                        }
                    })
                    .fail(function(xhr) {
                        console.error('Request failed:', xhr);
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);

                        let errorMessage = 'Failed to update task';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                const errors = Object.values(xhr.responseJSON.errors).flat();
                                errorMessage = errors.join(', ');
                            }
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: errorMessage
                        });
                    });
            });

            // View Task
            $(document).on('click', '.view-btn', function() {
                const id = $(this).data('id');
                // Always fetch the latest task data from API to get scheduled_at and images
                $.get(BASE_URL + '/api/tasks/' + id, function(res) {
                    if (res.success && res.task) {
                        $('#viewTaskName').text(res.task.name);
                        // Description
                        if (res.task.description && res.task.description.trim() !== '') {
                            $('#viewTaskDescription').html(fixImageUrls(res.task.description));
                        } else {
                            $('#viewTaskDescription').html(
                                '<p class="text-muted"><em>No description available</em></p>');
                        }
                        // Additional Description
                        if (res.task.description2 && res.task.description2.trim() !== '') {
                            $('#viewTaskDescription2').html(fixImageUrls(res.task.description2));
                        } else {
                            $('#viewTaskDescription2').html(
                                '<p class="text-muted"><em>No additional description</em></p>');
                        }
                        // Scheduled Meeting
                        if (res.task.scheduled_at) {
                            $('#viewTaskScheduled').text(formatDate(res.task.scheduled_at));
                            $('#viewTaskScheduledContainer').show();
                        } else {
                            $('#viewTaskScheduled').text('');
                            $('#viewTaskScheduledContainer').hide();
                        }
                        // Images
                        if (res.task.images && res.task.images.length > 0) {
                            let imagesHtml = '';
                            res.task.images.forEach(function(imgPath) {
                                imagesHtml +=
                                    `<img src='${BASE_URL}/storage/${imgPath}' alt='Task Image' style='max-width:120px;max-height:120px;margin:4px;border-radius:6px;box-shadow:0 1px 4px rgba(0,0,0,0.08);cursor:pointer;' class='task-modal-image'>`;
                            });
                            $('#viewTaskImages').html(imagesHtml);
                            $('#viewTaskImagesContainer').show();
                        } else {
                            $('#viewTaskImages').html('');
                            $('#viewTaskImagesContainer').hide();
                        }
                        // Set Full Task View button href with base URL
                        $('#fullTaskViewBtn').attr('href', BASE_URL + '/tasks/' + id +
                            '/full-view');
                        viewModal.show();
                        // Add image click handlers for zoom functionality after modal is shown
                        setTimeout(function() {
                            $('#viewTaskDescription img').off('click').on('click', function(
                                e) {
                                e.stopPropagation();
                                const imgSrc = $(this).attr('src');
                                $('#zoomedImage').attr('src', imgSrc);
                                $('#imageZoomModal').fadeIn(200);
                            });
                            // Handle broken images
                            $('#viewTaskDescription img').off('error').on('error',
                                function() {
                                    $(this).attr('alt', 'Image not found').css({
                                        'background-color': '#f8f9fa',
                                        'border': '1px dashed #dee2e6',
                                        'padding': '20px',
                                        'text-align': 'center',
                                        'color': '#6c757d'
                                    });
                                });
                        }, 300);
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
                        const description2 = res.description2 ||
                            ''; // Get second description from server response

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
                                .data('description', description)
                                .data('description2', description2);

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
                                        <button class="btn btn-sm btn-outline-primary me-2 edit-btn" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
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
                                .data('description', description)
                                .data('description2', description2);

                            $pendingTask.find('small').text(
                                `Created: ${formatDate(res.created_at)}`);

                            // Set data attributes for buttons
                            $pendingTask.find('.view-btn')
                                .data('id', id)
                                .data('name', name)
                                .data('description', description)
                                .data('description2', description2);

                            $pendingTask.find('.edit-btn')
                                .data('id', id)
                                .data('name', name)
                                .data('description', description)
                                .data('description2', description2);

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

                // Clear second description field (CKEditor5) - handled by the modal event above
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
