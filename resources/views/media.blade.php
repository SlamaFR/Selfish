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
@endsection

@section('content')
@include('components.navbar-media')

<div class="my-5 text-center h-100">
    @if($media_type == 'image')
        <img src="{{ $media_path }}" alt="">
    @elseif($media_type == 'audio')
        <div style="margin-top: 40vh">
            <audio id="player" playsinline controls>
                <source src="{{ $media_path }}" type="{{ $mime_type }}" />
            </audio>
        </div>
    @elseif($media_type == 'video')
        <video id="player" playsinline controls>
            <source src="{{ $media_path }}" type="{{ $mime_type }}" />
        </video>
    @endif
    <p class="my-3">{{ $file->media_name }}</p>
</div>
@endsection

@section('script')
<script>
    new ClipboardJS('.copy');
    let baseUrl = "{{ Config::get("app.url") }}";
    const csrf_token = "{{ csrf_token() }}";
    const player = new Plyr('#player', {
        ratio: "16:9"
    });
</script>
<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
@endsection
