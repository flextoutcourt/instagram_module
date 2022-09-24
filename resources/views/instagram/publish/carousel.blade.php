@extends('layouts.base')
@section('content')
    <form action="{{route('instagram.publish.post.post')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="container">
            <h1 class="h1">Publier une ou plusieurs images sur Instagram</h1>
            <div class="form-group">
                <label for="caption">Description</label>
                <input type="form-control" name="caption">
            </div>
            <div class="form-group">
                <label for="media">Images</label>
                <input type="file" class="form-control-file" name="medias[]" multiple>
            </div>
            <input type="submit" class="btn btn--primary">
        </div>
    </form>
@endsection
