<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Full Task View - {{ $task->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fa;
            padding: 2rem;
        }

        .task-header {
            background-color: #212529;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .task-section-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .task-image {
            border-radius: 0.75rem;
            box-shadow: 0 4px 16px rgba(44, 62, 80, 0.10);
            border: 2px solid #e0e7ef;
            width: 180px;
            height: 180px;
            object-fit: cover;
            background: #f8fafc;
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .task-image:hover {
            transform: scale(1.05);
        }

        .image-preview-global {
            position: fixed;
            top: 120px;
            /* moved down */
            right: 60px;
            /* moved inward */
            z-index: 999;
            border: 2px solid #ced4da;
            border-radius: 0.75rem;
            background-color: white;
            padding: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            display: none;
        }

        .image-preview-global img {
            width: 600px;
            /* increased width */
            height: 600px;
            /* increased height */
            object-fit: cover;
            border-radius: 0.5rem;
        }


        .description-box {
            background-color: transparent;
            border: none;
            padding: 0;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .image-preview-global {
                right: 10px;
                top: auto;
                bottom: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="task-header">
        <h4 class="mb-0">Title: {{ $task->name }}</h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-light btn-sm">Back</a>
    </div>

    <div class="container px-0">
        <div class="mb-3">
            <h6 class="fw-bold">Description</h6>
            <div class="description-box">{!! $task->description !!}</div>
        </div>

        <div class="mb-3">
            <h6 class="fw-bold">Additional Description</h6>
            <div class="description-box">{!! $task->description2 !!}</div>
        </div>

        @if ($task->images && $task->images->count())
            <div class="task-section-title">Uploaded Images</div>
            <div class="d-flex flex-wrap gap-3 mb-3">
                @foreach ($task->images as $img)
                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="Task Image" class="task-image"
                        onmouseover="showPreview('{{ asset('storage/' . $img->image_path) }}')"
                        onmouseout="hidePreview()">
                @endforeach
            </div>
        @endif
    </div>

    <!-- Image Hover Preview Box -->
    <div id="global-preview" class="image-preview-global">
        <img src="" alt="Preview" id="preview-img">
    </div>

    <script>
        const previewBox = document.getElementById('global-preview');
        const previewImg = document.getElementById('preview-img');

        function showPreview(src) {
            previewImg.src = src;
            previewBox.style.display = 'block';
        }

        function hidePreview() {
            previewBox.style.display = 'none';
        }
    </script>z

</body>

</html>
