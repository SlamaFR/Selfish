@extends('layouts.app', ['title' => __('general.page.upload')])

@section('head.scripts')
<script src="{{ asset('js/dropzone.min.js') }}"></script>
@endsection

@section('head.styles')
<link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
<style>
    .dropzone {
        border-radius: .2rem;
        border: 1px dashed; 
    }

    .dz-image {
        border-radius: .2rem!important;
    }
</style>
@endsection

@section('content')
@include('components.navbar')

<div class="container my-3">

    <div class="alert alert-info alert-important" role="alert">
        <h5>@lang('upload.card.notice')</h5>
        @lang('upload.card.message', ['size' => $max_upload_size])
    </div>

    <div class="card text-justify">
        <div class="card-body">
            <form action="{{ route('upload') }}" method="post" id="upload-dropzone" class="form-control dropzone dz-clickable" enctype="multipart/form-data">
                @csrf
                <div class="dz-default dz-message" name="file">
                    <button class="dz-button" type="button">@lang('upload.dropzone.title')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
