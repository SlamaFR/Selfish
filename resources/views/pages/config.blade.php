@extends('layouts.app')

@section('head.styles')
<style>
    .table-bordered>:not(caption)>*>:first-child,
    .table-bordered>:not(caption)>*>:last-child,
    .table-bordered>:not(caption)>:last-child {
        border-left-width: 0;
        border-bottom-width: 0;
        border-right-width: 0;
    }

    .table > tbody > tr:last-child > td:first-child {
        border-bottom-left-radius: .25rem;
    }

    .table > tbody > tr:last-child > td:last-child {
        border-bottom-right-radius: .25rem;
    }
    
    .file-icon {
        height: 32px;
        width: 32px;
    }

    .col-stats .feather {
        width: 32px;
        height: 32px;
    }
    
    .form-signin .form-control {
        position: relative;
        box-sizing: border-box;
        height: auto;
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
@include('components.navbar')
@include('components.toast')

@include('components.delete-modal', ['message' => 'Are you sure you want to delete this user?'])

<div class="modal fade" tabindex="-1" id="register-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">New user</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('admin.user.create') }}" method="POST" class="form-signin">
            @csrf
            <div class="modal-body">
                @include('components.register-form')
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary btn-icon"><i data-feather="plus"></i>Create</button>
            </div>
        </form>
      </div>
    </div>
  </div>

<div class="container">
    @include('flash::message')
    <div class="row">
        <div class="col-8">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <span class="col my-auto">Users</span>
                        <div class="col d-flex justify-content-end pe-1">
                            <button class="btn btn-sm btn-outline-primary btn-icon" id="new-user">
                                <i data-feather="plus"></i>New
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" style="margin: -1px;">
                    <table class="table table-bordered table-hover mb-0">
                        <thead>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Admin</th>
                            <th colspan="2">Email address</th>
                        </thead>
                        <tbody>
                            @foreach(User::all() as $user)
                            <tr>
                                <td class="text-center">{{ $user->id }}</td>
                                <td>{{ $user->username }}</td>
                                <td class="text-center" data-state="{{ $user->id }}">
                                    <i class="text-{{ $user->admin ? "success" : "danger" }}" data-feather="{{ $user->admin ? "check-circle" : "x-circle" }}"></i>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td scope="col">
                                    <div class="col d-flex text-center btn-group" data-id="{{ $user->id }}">
                                        <a class="btn btn-sm px-1 btn-outline-secondary @if($user->super()) disabled @endif" title="Edit" href="{{ route('admin.user.edit', ['userId' => $user->id]) }}">
                                            <i data-feather="edit-2"></i>
                                        </a>
                                        <a class="btn btn-sm px-1 btn-outline-warning @if($user->super()) disabled @endif" data-action="toggle-admin" data-admin="{{ (int) $user->admin }}" title="{{ $user->admin ? "Demote" : "Promote" }}">
                                            <i data-feather="{{ $user->admin ? "shield-off" : "shield" }}"></i>
                                        </a>
                                        <a class="btn btn-sm px-1 btn-outline-danger @if($user->super()) disabled @endif" data-action="delete-user" title="Delete">
                                            <i data-feather="trash-2"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    General settings
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.config.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-form-label" for="app_locale">Language</label>
                                    <div class="col">
                                        <select name="app.locale" id="app_locale" class="form-select form-control">
                                            @foreach(Config::get('app.locales') as $locale => $name)
                                            <option value="{{ $locale }}" @if($locale == App::currentLocale()) selected @endif>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-form-label" for="app_default-theme">Default theme</label>
                                    <div class="col">
                                        <select name="app_default-theme" id="app_default-theme" class="form-select form-control">
                                            <option value="dark" @if($defaultTheme == 'dark') selected @endif>Dark</option>
                                            <option value="light" @if($defaultTheme == 'light') selected @endif>Light</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-form-label">Registrations</label>
                                    <div class="col my-auto">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="app_registrations" id="app_registration_enabled" value="1" @if($registrations) checked @endif>
                                            <label class="form-check-label" for="app_registration_enabled">Enabled</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="app_registrations" id="app_registratio_disabled" value="0" @if(!$registrations) checked @endif>
                                            <label class="form-check-label" for="app_registration_disabled">Disabled</label>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-form-label" for="disk_max-quota">Default maximum user disk quota</label>
                                    <div class="col">
                                        <div class="input-group">
                                            <input type="number" name="disk_max-quota" class="form-control" min="0" value="{{ $maxDiskQuota >> $maxDiskQuotaShift }}">
                                            <select name="disk_max-quota_unit" id="disk_max-quota_unit" class="form-select form-control">
                                                @foreach(['bytes', 'KiB', 'MiB', 'GiB'] as $index => $unit)
                                                <option value="{{ $index * 10 }}" @if($maxDiskQuotaShift == $index * 10) selected @endif>{{ $unit }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <small class="m-0 text-muted">0 means no limit.</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-form-label">reCAPTCHA login protection</label>
                                    <div class="col my-auto">
                                        <div class="pt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="app_captcha" id="app_captcha_enabled" value="1" @if($useCaptcha) checked @endif>
                                                <label class="form-check-label" for="app_captcha_enabled">Enabled</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="app_captcha" id="app_captcha_disabled" value="0" @if(!$useCaptcha) checked @endif>
                                                <label class="form-check-label" for="app_captcha_disabled">Disabled</label>
                                            </div>
                                        </div>
                                        <small class="text-muted">Only reCAPTCHA v3 is supported.</small>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-form-label">Site key</label>
                                    <div class="col">
                                        <input type="password" class="form-control hover-to-see @error('key_captcha_site') is-invalid @enderror" name="key_captcha_site" value="{{ Setting::get('key.captcha.site') }}" @if(!$useCaptcha) disabled @endif>
                                        @error('key_captcha_site')
                                        <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-sm-5 col-form-label">Private key</label>
                                    <div class="col">
                                        <input type="password" class="form-control hover-to-see @error('key_captcha_private') is-invalid @enderror" name="key_captcha_private" value="{{ Setting::get('key.captcha.private') }}" @if(!$useCaptcha) disabled @endif>
                                        @error('key_captcha_private')
                                        <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-success btn-icon"><i data-feather="save"></i>Save</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card mb-4">
                <div class="card-header">Informations</div>
                <div class="card-body">
                    <div class="row text-center pt-2">
                        <div class="col col-stats">
                            <i data-feather="file" class="w-100 mb-1" stroke-width="1.5"></i>
                            <span id="file-count">{{ Upload::count() }} files</span>
                        </div>
                        <div class="col col-stats">
                            <i data-feather="user" class="w-100 mb-1" stroke-width="1.5"></i>
                            <span id="user-count">{{ User::count() }} users</span> 
                        </div>
                        <div class="col col-stats">
                            <i data-feather="hard-drive" class="w-100 mb-1" stroke-width="1.5"></i>
                            <span id="total-usage">{{ Files::humanFileSize(User::all()->map(function ($item, $key) {return $item->disk_quota;})->sum()) }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="col">PHP Version: {{ PHP_VERSION }}</div>
                    <div class="col">Max post size: {{ Files::humanFileSize(Files::stringToBytes(ini_get('post_max_size'))) }}</div>
                    <div class="col">Max upload size: {{ Files::humanFileSize(Files::stringToBytes(ini_get('upload_max_filesize'))) }}</div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Maintenance</div>
                <div class="card-body">
                    <div class="d-grid">
                        <button class="btn btn-icon btn-outline-warning mb-2" data-action="clean-up">
                            <i data-feather="database"></i>Clean up orphaned files
                        </button>
                        <button class="btn btn-icon btn-outline-warning mb-2" data-action="recalculate-quotas">
                            <i data-feather="refresh-cw"></i>Recalculate quotas
                        </button>
                        <button class="btn btn-icon btn-outline-danger @if(Setting::get('app.maintenance')) active @endif" data-action="toggle-maintenance">
                            <i data-feather="power"></i>Maintenance mode
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    let registerModal = new bootstrap.Modal($('#register-modal').get(0));

    $('#new-user').click(function () {
        registerModal.show();
    });

</script>
@endsection

@section('script2')
@if($errors->has('username') || $errors->has('email') || $errors->has('password') || $errors->has('password_confirmation'))
<script>
    registerModal.show();
</script>
@endif
@endsection
