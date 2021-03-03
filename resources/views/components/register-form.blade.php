<label for="inputUsername" class="visually-hidden">@lang('auth.username')</label>
<input name="username" type="text" id="inputUsername" class="form-control @error('username') is-invalid @enderror" placeholder="@lang('auth.username')" required autofocus>
@error('username')
    <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror

<label for="inputEmail" class="visually-hidden">@lang('auth.email')</label>
<input name="email" type="email" id="inputEmail" class="form-control @error('email') is-invalid @enderror" placeholder="@lang('auth.email')" required>
@error('email')
    <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror

<label for="inputPassword" class="visually-hidden">@lang('auth.password')</label>
<input name="password" type="password" id="inputPassword" class="form-control @error('password') is-invalid @enderror" placeholder="@lang('auth.password')" required>

<label for="inputPasswordConfirmation" class="visually-hidden">@lang('auth.password.confirmation')</label>
<input name="password_confirmation" type="password" id="inputPasswordConfirmation" class="form-control @error('password') is-invalid @enderror" placeholder="@lang('auth.password.confirmation')" required>
@error('password')
    <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror