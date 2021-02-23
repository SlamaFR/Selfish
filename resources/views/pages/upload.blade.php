@extends('layouts.app')

@section('head')
<link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
<script src="{{ asset('js/dropzone.js') }}"></script>
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

    <div class="alert alert-info" role="alert">
        <h5>Notice</h5>
        You can currently upload files up to {{ $max_upload_size }} each.
    </div>

    <div class="card text-justify">
        <div class="card-body">
            <form action="{{ route('upload') }}" method="post" id="upload-dropzone" class="form-control dropzone dz-clickable" enctype="multipart/form-data">
                @csrf
                <div class="dz-default dz-message" name="file">
                    <button class="dz-button" type="button">Click or drop files here</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
