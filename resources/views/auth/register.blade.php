@extends('layouts.app')

@section('head')
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
    <form method="POST">
        @csrf
        
        <h1 class="mb-5 fw-normal" style="font-family: Pacifico, sans-serif;">{{ Config::get('app.name') }}</h1>

        <label for="inputUsername" class="visually-hidden">Username</label>
        <input name="username" type="text" id="inputUsername" class="form-control @error('username') is-invalid @enderror" placeholder="Username" required autofocus>
        @error('username')
            <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        
        <label for="inputEmail" class="visually-hidden">Email address</label>
        <input name="email" type="email" id="inputEmail" class="form-control @error('email') is-invalid @enderror" placeholder="Email address" required>
        @error('email')
            <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <label for="inputPassword" class="visually-hidden">Password</label>
        <input name="password" type="password" id="inputPassword" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>

        <label for="inputPasswordConfirmation" class="visually-hidden">Password confirmation</label>
        <input name="password_confirmation" type="password" id="inputPasswordConfirmation" class="form-control" placeholder="Password confirmation" required>
        @error('password')
            <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <div class="container mt-3">
            <div class="row">
                <div class="col text-end">
                    <a href="{{ route('login') }}">Already registered?</a>
                </div>
            </div>
        </div>
        
        <button class="mt-4 btn btn-lg btn-primary w-100" type="submit">Sign up</button>
    </form>
</main>
@endsection
