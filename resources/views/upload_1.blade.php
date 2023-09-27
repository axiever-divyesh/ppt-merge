<!DOCTYPE html>
<html>
<head>
    <title>Upload PowerPoint Files</title>
</head>
<body>
    <h2>Upload PowerPoint Files</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="/upload" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="pptx_files[]" multiple>
        <br><br>
        <button type="submit">Upload and Merge</button>
    </form>
</body>
</html>
