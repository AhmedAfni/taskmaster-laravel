<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Full Task View - {{ $task->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .task-image {
            border-radius: 0.75rem;
            box-shadow: 0 4px 16px rgba(44, 62, 80, 0.10);
            border: 2px solid #e0e7ef;
            margin-bottom: 1.5rem;
            max-height: 320px;
            object-fit: contain;
            background: #f8fafc;
            transition: transform 0.3s cubic-bezier(.4, 2, .3, 1);
            cursor: zoom-in;
        }

        .task-image:hover {
            transform: scale(1.12);
            z-index: 2;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow-lg mx-auto" style="max-width: 700px;">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">{{ $task->name }}</h3>
            </div>
            <div class="card-body">
                <h6 class="fw-bold">Description</h6>
                <div class="mb-3 border rounded p-2 bg-light">{!! $task->description !!}</div>
                <h6 class="fw-bold">Additional Description</h6>
                <div class="mb-3 border rounded p-2 bg-light">{!! $task->description2 !!}</div>
                @if ($task->image)
                    <div class="task-section-title">Image</div>
                    <img src="http://localhost/taskmaster_laravel/storage/{{ $task->image }}" alt="Task Image"
                        class="img-fluid task-image">
                @endif
                <div class="text-left mt-4">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary btn-back">Back</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
