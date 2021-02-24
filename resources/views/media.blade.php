@extends('layouts.app')

@php
use App\Models\User;
@endphp

@section('head.scripts')
<script type="text/javascript" src="{{ asset('js/plyr.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/clipboard.min.js') }}"></script>
@endsection

@section('head.styles')
<link href="{{ asset('css/plyr.css') }}" rel="stylesheet">
<style>
    .nav-link {
        cursor: pointer;
    }

    .pdf-viewer {
        width: 100%;
        height: 80vh;
    }

    body .container {
        max-width: 100%!important;
    }

    .wrapper {
        max-width: 1160px;
        margin: 0 auto;
    }

    h1 .file-icon {
        height: 72px;
        width: 72px;
    }
</style>
@endsection

@section('content')
@include('components.navbar-media')

@include('components.toast')
@include('components.delete-modal')

<div class="my-4 text-center h-100 mx-auto">
    @if($media_type == 'image')
        <div class="wrapper">
            <img src="{{ $media_path }}" class="img-fluid" alt="" style="max-width: 1160px;">
        </div>
        <p class="fw-bold my-3">{{ $file->media_name }}</p>
    @elseif($media_type == 'audio')
        <div class="wrapper" style="margin-top: 40vh">
            <audio id="player" autoplay controls loop preload="auto">
                <source src="{{ $media_path }}" type="{{ $mime_type }}" />
            </audio>
        </div>
        <p class="fw-bold my-3">{{ $file->media_name }}</p>
    @elseif($media_type == 'video')
        <div class="wrapper">
            <video id="player" autoplay controls loop preload="auto">
                <source src="{{ $media_path }}" type="{{ $mime_type }}" />
            </video>
        </div>
        <p class="fw-bold my-3">{{ $file->media_name }}</p>
    @elseif($media_type == 'pdf')
        <div class="container px-4">
            <embed class="pdf-viewer" src="{{ $media_path }}" type="{{ $mime_type}}">
        </div>
        <p class="fw-bold my-3">{{ $file->media_name }}</p>
    @else
        <h1 class="mt-5"><i class="file-icon" data-feather="{{ $file->icon() }}"></i></h1>
        <p class="fw-bold my-3 mb-0">{{ $file->media_name }}</p>
        <p><small>{{ Files::humanFileSize(Storage::disk('public')->size($file->path())) }}</small></p>
        <a class="btn btn-lg btn-icon btn-secondary" href="{{ route('media.download', ['mediaCode' => $media_code]) }}">
            <i data-feather="download"></i>Download
        </a>
    @endif
</div>
@endsection

@section('script')
<script>
    const player = new Plyr('#player', {
        ratio: "16:9"
    });
</script>
@endsection
