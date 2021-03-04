@extends('layouts.app', ['title' => __('general.page.signin')])

@section('head.scripts')
<script src="https://www.google.com/recaptcha/api.js"></script>
@endsection

@section('head.styles')
<style>
    html,
    body {
        height: 100%;
    }

    body {
        display: flex;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
    }

    .form-signin {
        width: 100%;
        max-width: 460px;
        padding: 15px;
        margin: auto;
    }

    .form-signin .checkbox {
        font-weight: 400;
    }

    .form-signin .form-control {
        position: relative;
        box-sizing: border-box;
        height: auto;
        padding: 10px;
        font-size: 16px;
    }

    .form-signin .form-control:focus {
        z-index: 2;
    }

    .form-signin input[name="username"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    
    .form-signin input[name="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
</style>
@endsection

@section('content')
<main class="form-signin text-center">
    <form method="POST" id="login-form">
        @csrf

        <h1 class="mb-5 fw-normal" style="font-family: Pacifico, sans-serif;">{{ Config::get('app.name') }}</h1>

        @error('username')
        <div class="text-start alert alert-danger alert-dismissible fade show" role="alert">
            <h5>@lang('general.error')</h5>
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @enderror

        @if(Setting::get('app.maintenance'))
        <div class="alert alert-warning alert-important fade show" role="alert">
            @lang('auth.maintenance')
        </div>
        @endif

        @include('flash::message')

        <label for="inputusername" class="visually-hidden">@lang('auth.username')</label>
        <input name="username" type="text" id="inputusername" class="form-control" placeholder="@lang('auth.username')" value="{{ old('username') }}" required @if(!old('username')) autofocus @endif>
        <label for="inputPassword" class="visually-hidden">@lang('auth.password')</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="@lang('auth.password')" required @if(old('username')) autofocus @endif>

        <div class="container">
            <div class="row">
                <div class="col text-start">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberCheck">
                        <label class="form-check-label" for="rememberCheck">
                            @lang('auth.remember')
                        </label>
                      </div>
                </div>
                <div class="col text-end">
                    <a href="{{ route('password.request') }}">@lang('auth.link.forgot-password')</a>
                    @if(Setting::get('app.registrations') == '1')
                    <br>
                    <a href="{{ route('register') }}">@lang('auth.link.signup')</a>
                    @endif
                </div>
            </div>
        </div>
        @if(Setting::get('app.captcha') == '1')
        <button class="mt-4 btn btn-lg btn-primary w-100 g-recaptcha" data-sitekey="{{ Setting::get('key.captcha.site') }}" data-callback='onSubmit' data-action='submit'>@lang('auth.btn.login')</button>
        @else
        <button class="mt-4 btn btn-lg btn-primary w-100" type='submit'>@lang('auth.btn.login')</button>
        @endif
    </form>
</main>
@endsection

@if(Setting::get('app.captcha') == '1')
@section('script')
<script>
    function onSubmit(token) {
        document.getElementById("login-form").submit();
    }
</script>
@endsection
@endif
