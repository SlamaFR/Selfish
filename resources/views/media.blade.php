@extends('layouts.app', ['title' => $file->media_name])

@php
use App\Models\User;
@endphp

@section('head.scripts')
<script type="text/javascript" src="{{ asset('js/plyr.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/clipboard.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/highlight.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/highlight-lines.min.js') }}"></script>
@endsection

@section('head.styles')
<link href="{{ asset('css/plyr.css') }}" rel="stylesheet">
<link href="{{ asset('css/atom-one-dark.css') }}" rel="stylesheet">
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

    .hljs-ln-numbers {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        text-align: right;
        color: #7d7d7d;
        border-right: 1px solid #4b4b4b;
        vertical-align: top;
        padding-right: 5px!important;
    }

    .hljs-ln-code {
        padding-left: 5px!important;
	    white-space: pre;
    }
</style>
@endsection

@section('content')
@include('components.navbar-media')

@include('components.toast')
@include('components.delete-modal', ['message' => __('media.delete-modal.question')])

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
            <embed class="pdf-viewer" src="{{ $media_path }}" type="{{ $mime_type }}">
        </div>
        <p class="fw-bold my-3">{{ $file->media_name }}</p>
    @elseif($media_type == 'text')
        <div class="container px-2 text-start">
            @php($fileExplode = explode('.', $file->media_name))
            <pre><code @if(end($fileExplode) === 'txt') class="plaintext" @endif>{{ $media_raw }}</code></pre>
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
    hljs.initHighlightingOnLoad();
    hljs.initLineNumbersOnLoad({
        singleLine: true
    });
</script>
@endsection
