@extends('base')

@section('head')
<style>
    .content {
        position: fixed;
        align-items: center;
        text-align: center;
        left: 0;
        right: 0;
        z-index: 2;
    }

    html,
    body {
        height: 100%;
    }

    body {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
    }

    .form-signin {
        width: 100%;
        max-width: 440px;
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

    .form-signin input[type="text"] {
        margin-bottom: 10px;
    }

    .form-signin input[type="email"] {
        margin-bottom: 10px;
    }

    .form-signin input[name="password"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }

    .form-signin input[name="password_confirmation"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .title {
        font-family: Pacifico;
        font-size: 3em;
    }
</style>
@endsection

@section('body')
<div class="content">
    <form class="form-signin d-grip" method="post">
        {{ csrf_field() }}

        <h1 class="h2 mb-5 font-weight-normal title">{{ Config::get('app.name') }}</h1>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show text-start" role="alert" id="alert">
            <h5>Erreur</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(isset($usernameExists))
        <div class="alert alert-danger alert-dismissible fade show text-start" role="alert" id="alert">
            <h5>Erreur</h5>
            Ce nom d'utilisateur est déjà pris.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(isset($emailExists))
        <div class="alert alert-danger alert-dismissible fade show text-start" role="alert" id="alert">
            <h5>Erreur</h5>
            Cette adresse e-mail est déjà prise.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <input name="username" type="text" class="form-control" placeholder="Nom d'utilisateur" autofocus required>
        <input name="email" type="email" class="form-control" placeholder="Courriel" autofocus required>
        <input name="password" type="password" class="form-control" placeholder="Mot de passe" required>
        <input name="password_confirmation" type="password" class="form-control" placeholder="Confirmation du mot de passe" required>
        
        <div class="container mt-3 mb-4">
            <div class="row">
                <div class="col text-end">
                    <a href="{{ route('login') }}">Vous possédez déjà un compte ?</a>
                </div>
            </div>
        </div>

        <div class="d-grid gap-2">
            <button class="btn btn-primary btn-lg" type="submit">Inscription</button>
        </div>
    </form>
</div>

@endsection
