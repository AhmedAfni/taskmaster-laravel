<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Full Task View - {{ $task->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

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

        .main-flex-layout {
            display: flex;
            gap: 2.5rem;
            align-items: flex-start;
        }

        .main-content-area {
            padding-left: 2rem;
            padding-right: 2rem;
            max-width: 700px;
        }

        .description-box {
            background-color: transparent;
            border: none;
            padding: 0;
            margin-bottom: 2rem;
        }

        .zoom-container {
            position: relative;
            width: 180px;
            height: 180px;
            border-radius: 0.75rem;
            box-shadow: 0 4px 16px rgba(44, 62, 80, 0.10);
            border: 2px solid #e0e7ef;
            background-repeat: no-repeat;
            background-size: cover;
            cursor: pointer;
            overflow: hidden;
            transition: box-shadow 0.2s, border-color 0.2s;
        }

        .zoom-container:hover {
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
            border-color: #b6c6e3;
        }

        .zoom-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.75rem;
            display: block;
            user-select: none;
            pointer-events: none;
        }

        #image-preview-area {
            width: 100%;
            aspect-ratio: 1/1;
            border-radius: 1.2rem;
            box-shadow: 0 12px 48px rgba(44, 62, 80, 0.18);
            border: 3px solid #e0e7ef;
            background-repeat: no-repeat;
            background-size: 200%;
            background-position: center center;
            background-color: #f8fafc;
            transition: background-position 0.2s, opacity 0.2s, box-shadow 0.2s, border-color 0.2s;
            position: relative;
            margin-bottom: 2rem;
            background-image: linear-gradient(135deg, #e0e7ef 0%, #f8fafc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #image-preview-area.active {
            box-shadow: 0 16px 64px rgba(44, 62, 80, 0.22);
            border-color: #b6c6e3;
        }

        @media (max-width: 992px) {
            .main-content-area {
                padding-left: 1rem;
                padding-right: 1rem;
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .main-flex-layout {
                flex-direction: column;
            }

            #image-preview-area {
                width: 100%;
                min-height: 200px;
                aspect-ratio: unset;
            }
        }
    </style>
</head>

<body>
    <div class="task-header">
        <h4 class="mb-0">Title: {{ $task->name }}</h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-light btn-sm">Back</a>
    </div>
    <div class="main-flex-layout">
        <div class="flex-grow-1 main-content-area">
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
                        <div class="zoom-container"
                            style="background-image: url('{{ asset('storage/' . $img->image_path) }}')"
                            onmousemove="zoomMove(event, this)" onmouseleave="zoomOut(this)"
                            onmouseenter="showPreview(event, '{{ asset('storage/' . $img->image_path) }}', this)">
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="Task Image" draggable="false" />
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div style="min-width: 420px; max-width: 480px; width: 32vw;">
            <div id="image-preview-area" style="display:none;"></div>
        </div>
    </div>
    <script>
        function zoomMove(e, container) {
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const xPercent = (x / rect.width) * 100;
            const yPercent = (y / rect.height) * 100;
            const preview = document.getElementById('image-preview-area');
            if (preview.style.display !== 'none') {
                preview.style.backgroundPosition = `${xPercent}% ${yPercent}%`;
            }
        }

        function zoomOut(container) {
            const preview = document.getElementById('image-preview-area');
            preview.style.display = 'none';
            preview.style.backgroundImage = '';
            preview.classList.remove('active');
        }

        function showPreview(e, imgUrl, container) {
            const preview = document.getElementById('image-preview-area');
            preview.style.display = 'block';
            preview.style.backgroundImage = `url('${imgUrl}')`;
            preview.style.backgroundPosition = 'center center';
            zoomMove(e, container);
            preview.classList.add('active');
        }
    </script>
</body>

</html>
