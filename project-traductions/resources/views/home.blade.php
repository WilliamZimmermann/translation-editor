<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

    </head>
    <body>
        @if(session()->get('message'))
            <div class="alert alert-success">
            {{ session()->get('message') }}
            </div>
        @endif
        <form class="row g-3" method="POST" action="{{ route('file.upload') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="col-12">
                <label class="form-label">Original language</label>
                <input type="text" name="originalLanguage" id="originalLanguage" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label">Upload one or more files</label>
                <input type="file" name="filesToUpload[]" id="filesToUpload[]" class="form-control" multiple>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Send File</button>
            </div>
        </form>
        @if(session()->get('content'))
            <label class="form-label">Unifed Extracted JSON File</label>
            <textarea name="fileExtractedStrings" class="form-control">{{ session()->get('content') }}</textarea>
        @endif
    </body>
</html>
