@extends('layouts.base')
@section('content')
    <form action="{{ route('instagram.publish.post.post') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="container">
            <div class="card__custom">
                <div class="card__custom-header">
                    <h1 class="display-4">Publier sur Instagram</h1>
                </div>
                <div class="card__custom-body">
                    <div class="card__custom-label__container">
                        <label for="medias" class="card__custom-label" id="medias_label" />
                        <input type="file" class="form-control-file d-none" name="medias[]" id="medias" multiple>
                    </div>
                    <input type="text" placeholder="Entrez votre description" class="card__custom-input" name="caption">
                    <input type="submit" class="card__custom-submit" id="input_submit" disabled>
                </div>
            </div>
        </div>
    </form>
@endsection
