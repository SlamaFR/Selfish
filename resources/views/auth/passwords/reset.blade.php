@extends('layouts.app', ['title' => __('general.page.password.reset')])

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

    .form-signin input[name="email"] {
        margin-top: 10px;
    }

    .form-signin input[name="password"] {
        margin-top: 10px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .form-signin input[name="password_confirmation"] {
        margin-top: -1px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
</style>
@endsection

@section('content')
<main class="form-signin text-center">
    <form method="POST" id="update-form" action="{{ route('password.update') }}">
        @csrf
        <h1 class="mb-5 fw-normal" style="font-family: Pacifico, sans-serif;">{{ Config::get('app.name') }}</h1>

        @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
        @endif

        <input type="hidden" name="token" value="{{ $token }}">

        <label for="inputemail" class="visually-hidden">@lang('auth.email')</label>
        <input name="email" type="email" id="inputemail" class="form-control @error('email') is-invalid @enderror" placeholder="@lang('auth.email')" value="{{ $email ?? old('email') }}" required @if(!$email ?? true) autofocus @endif>
        @error('email')
        <span class="invalid-feedback ms-1 text-start" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror

        <label for="inputpassword" class="visually-hidden">@lang('auth.password')</label>
        <input name="password" type="password" id="inputpassword" class="form-control @error('password') is-invalid @enderror" placeholder="@lang('auth.password')" required @if($email ?? false) autofocus @endif>
        <label for="inputpassword2" class="visually-hidden">@lang('auth.password.confirmation')</label>
        <input name="password_confirmation" type="password" id="inputpassword2" class="form-control @error('password') is-invalid @enderror" placeholder="@lang('auth.password.confirmation')" required>
        @error('password')
        <span class="invalid-feedback ms-1 text-start" role="alert">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
        
        @if(Setting::get('app.captcha') == '1')
        <button class="mt-4 btn btn-lg btn-primary w-100 g-recaptcha" data-sitekey="{{ Setting::get('key.captcha.site') }}" data-callback='onSubmit' data-action='submit'>@lang('auth.password.reset')</button>
        @else
        <button class="mt-4 btn btn-lg btn-primary w-100" type='submit'>@lang('auth.password.reset')</button>
        @endif
    </form>
</main>
@endsection

@if(Setting::get('app.captcha') == '1')
@section('script')
<script>
    function onSubmit(token) {
        document.getElementById("update-form").submit();
    }
</script>
@endsection
@endif
