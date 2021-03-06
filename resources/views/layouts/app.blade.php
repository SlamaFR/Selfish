<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ Config::get('app.name') }} &nbsp;&bull;&nbsp; {{ $title ?? '' }}</title>

    <!-- Styles -->
    <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    {{-- @yield('head.scripts') --}}

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('head.styles')
    @if(Setting::get('app.maintenance'))
    <style>
        .navbar {
            border-bottom: 2px solid #dc3545;
        }
    </style>
    @endif
</head>
<body class="mx-auto" data-theme="{{ Cookie::get('theme', Setting::get('app.default_theme')) }}">
@yield('content')

@yield('script')
<script>
    const csrf_token = "{{ csrf_token() }}";
    const baseUrl = "{{ Config::get("app.url") }}";
</script>

{{-- <p class="text-end me-5"><span style="font-family: Pacifico">Selfish</span> v1.0</p> --}}
</body>
</html>
