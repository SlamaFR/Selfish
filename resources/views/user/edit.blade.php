@extends('layouts.app', ['title' => ($self ? __('general.page.settings.self') : __('general.page.settings', ['username' => $user->username]))])

@section('content')
@include('components.navbar')
@include('components.toast')

<div class="container">
    @include('flash::message')
    <div class="row">
        <div class="col-8">
            <div class="card mb-4">
                <div class="card-header">
                    @lang('settings.user.title')
                </div>
                <div class="card-body">
                    <form action="{{ $self ? route('user.settings.info') : route('admin.user.settings.info', ['userId' => $user->id]) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">@lang('settings.user.code')</label>
                            <div class="col">
                                <input type="text" value="{{ $user->code }}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">@lang('settings.user.username')</label>
                            <div class="col">
                                <input name="username" type="text" value="{{ $user->username }}" class="form-control @error('username') is-invalid @enderror">
                                @error('username')
                                    <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">@lang('settings.user.email')</label>
                            <div class="col">
                                <input name="email" type="email" value="{{ $user->email }}" class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                    <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">@lang('settings.user.token')</label>
                            <div class="col">
                                <div class="input-group">
                                    <input type="password" class="form-control hover-to-see" value="{{ $user->access_token }}" id="personnal-token" readonly>
                                    <button class="btn btn-icon btn-outline-secondary copy" type="button" data-clipboard-target="#personnal-token">
                                        <i data-feather="clipboard"></i>@lang('settings.user.token.copy')
                                    </button>
                                    <button class="btn btn-icon btn-outline-primary" type="button" data-action="regenerate-token" @if(!$self) data-id="{{ $user->id }}" @endif>
                                        <i data-feather="refresh-cw"></i>@lang('settings.user.token.regenerate')
                                    </button>
                                </div>
                                <small class="text-muted">@lang('general.hover.to.see')</small>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">@lang('settings.user.quota')</label>
                            <div class="col my-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="disk_max-quota" id="disk_max-quota_default" value="default" @if($user->settings()->get('disk.max_quota') == 'default') checked @endif @if(!Auth::user()->admin) disabled @endif>
                                    <label class="form-check-label" for="disk_max-quota_default">@lang('settings.user.quota.default')</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="disk_max-quota" id="disk_max-quota_custom" value="custom" @if($user->settings()->get('disk.max_quota') == 'custom') checked @endif @if(!Auth::user()->admin) disabled @endif>
                                    <label class="form-check-label" for="disk_max-quota_custom">@lang('settings.user.quota.custom')</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">@lang('settings.user.custom-quota')</label>
                            <div class="col">
                                <div class="input-group">
                                    <input type="number" name="disk_custom-max-quota" id="disk_custom-max-quota" class="form-control" min="0" @if($user->settings()->get('disk.max_quota') == 'default' || !Auth::user()->admin) disabled @endif value="{{ $maxDiskQuota >> $maxDiskQuotaShift }}">
                                    <select name="disk_custom-max-quota_unit" id="disk_custom-max-quota_unit" class="form-select form-control" @if($user->settings()->get('disk.max_quota') == 'default' || !Auth::user()->admin) disabled @endif>
                                        @foreach(['bytes', 'KiB', 'MiB', 'GiB'] as $index => $unit)
                                            <option value="{{ $index * 10 }}" @if($maxDiskQuotaShift == $index * 10) selected @endif>@lang('units.' . $unit)</option>
                                        @endforeach
                                    </select>
                                </div>
                                <small class="text-muted">@lang('config.settings.default-quota.caption')</small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">@lang('settings.user.auto-delete')</label>
                            <div class="col my-auto">
                                <div class="pt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="disk_auto-delete" id="disk_auto-delete_enabled" value="1" @if($user->settings()->get('disk.auto_delete') == '1') checked @endif>
                                        <label class="form-check-label" for="disk_auto-delete_enabled">@lang('config.state.enabled')</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="disk_auto-delete" id="disk_auto-delete_disabled" value="0" @if($user->settings()->get('disk.auto_delete') == '0') checked @endif>
                                        <label class="form-check-label" for="disk_auto-delete_disabled">@lang('config.state.disabled')</label>
                                    </div>
                                </div>
                                <small class="text-muted">@lang('settings.user.auto-delete.caption')</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-icon btn-outline-success"><i data-feather="save"></i>@lang('general.save')</button>
                    </form>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    @lang('settings.password.title')
                </div>
                <div class="card-body">
                    <form action="{{ $self ? route('user.settings.password') : route('admin.user.settings.password', ['userId' => $user->id]) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="oldPassword" class="col-sm-4 col-form-label">@lang('settings.password.old')</label>
                            <div class="col">
                                <input type="password" class="form-control @error('old_password') is-invalid @enderror" id="oldPassword" name="old_password" @if(!$self) disabled @endif>
                                @error('old_password')
                                    <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="newPassword" class="col-sm-4 col-form-label">@lang('settings.password.new')</label>
                            <div class="col">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="newPassword" name="new_password">
                                @error('new_password')
                                    <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="confirmPassword" class="col-sm-4 col-form-label">@lang('settings.password.confirmation')</label>
                            <div class="col">
                                <input type="password" class="form-control" id="confirmPassword" name="new_password_confirmation">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-icon btn-outline-success"><i data-feather="save"></i>@lang('general.save')</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    @lang('settings.display.title')
                </div>
                <div class="card-body">
                    <form action="{{ $self ? route('user.settings.display') : route('admin.user.settings.display', ['userId' => $user->id]) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="image"></i>@lang('settings.display.pictures')</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.image" id="1.default" value="default" autocomplete="off" @if($user->settings()->get('display.image') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="1.default">@lang('settings.display.default')</label>
                                <input type="radio" class="btn-check" name="display.image" id="1.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.image') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="1.raw">@lang('settings.display.raw')</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="video"></i>@lang('settings.display.videos')</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.video" id="2.default" value="default" autocomplete="off" @if($user->settings()->get('display.video') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="2.default">@lang('settings.display.default')</label>
                                <input type="radio" class="btn-check" name="display.video" id="2.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.video') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="2.raw">@lang('settings.display.raw')</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="music"></i>@lang('settings.display.audios')</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.audio" id="3.default" value="default" autocomplete="off" @if($user->settings()->get('display.audio') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="3.default">@lang('settings.display.default')</label>
                                <input type="radio" class="btn-check" name="display.audio" id="3.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.audio') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="3.raw">@lang('settings.display.raw')</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="file-text"></i>@lang('settings.display.text')</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.text" id="4.default" value="default" autocomplete="off" @if($user->settings()->get('display.text') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="4.default">@lang('settings.display.default')</label>
                                <input type="radio" class="btn-check" name="display.text" id="4.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.text') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="4.raw">@lang('settings.display.raw')</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="file-text"></i>@lang('settings.display.pdf')</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.pdf" id="5.default" value="default" autocomplete="off" @if($user->settings()->get('display.pdf') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="5.default">@lang('settings.display.default')</label>
                                <input type="radio" class="btn-check" name="display.pdf" id="5.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.pdf') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="5.raw">@lang('settings.display.raw')</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="archive"></i>@lang('settings.display.archives')</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.zip" id="6.default" value="default" autocomplete="off" @if($user->settings()->get('display.zip') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="6.default">@lang('settings.display.default')</label>
                                <input type="radio" class="btn-check" name="display.zip" id="6.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.zip') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="6.raw">@lang('settings.display.raw')</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="file"></i>@lang('settings.display.other')</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.file" id="7.default" value="default" autocomplete="off" @if($user->settings()->get('display.file') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="7.default">@lang('settings.display.default')</label>
                                <input type="radio" class="btn-check" name="display.file" id="7.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.file') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="7.raw">@lang('settings.display.raw')</label>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary" data-action="set-display-default" >@lang('settings.display.all-default')</button>
                        <button type="submit" class="btn btn-icon btn-outline-success"><i data-feather="save"></i>@lang('general.save')</button>
                    </form>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    @lang('settings.sharex.title')
                </div>
                <div class="card-body">
                    <p style="text-align: justify;">
                        @lang('settings.sharex.summary')
                    </p>
                    <div class="d-grid">
                        <button class="btn btn-icon btn-outline-{{ Cookie::get('theme', Setting::get('app.default_theme')) == 'dark' ? 'light' : 'dark' }}" @if($self) data-action="download-sharex" @else disabled @endif>
                            <i data-feather="download"></i>@lang('settings.sharex.btn')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
