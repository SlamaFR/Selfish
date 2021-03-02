@extends('layouts.app')

@section('head.styles')
<style>
    td {
        padding: 8px!important;
    }

    legend {
        padding: 0!important;
    }

    label .feather {
        margin-right: .5rem;
    }
</style>
@endsection

@section('content')
@include('components.navbar')
@include('components.toast')

<div class="container">
    @include('flash::message')
    <div class="row">
        <div class="col-8">
            <div class="card mb-4">
                <div class="card-header">
                    User settings
                </div>
                <div class="card-body">
                    <form action="{{ $self ? route('user.settings.info') : route('admin.user.settings.info', ['userId' => $user->id]) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">User code</label>
                            <div class="col">
                                <input type="text" value="{{ $user->code }}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Username</label>
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
                            <label class="col-sm-4 col-form-label">Email address</label>
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
                            <label class="col-sm-4 col-form-label">Personnal token</label>
                            <div class="col">
                                <div class="input-group">
                                    <input type="password" class="form-control hover-to-see" value="{{ $user->access_token }}" id="personnal-token" readonly>
                                    <button class="btn btn-icon btn-outline-secondary copy" type="button" data-clipboard-target="#personnal-token">
                                        <i data-feather="clipboard"></i>Copy
                                    </button>
                                    <button class="btn btn-icon btn-outline-primary" type="button" data-action="regenerate-token" @if(!$self) data-id="{{ $user->id }}" @endif>
                                        <i data-feather="refresh-cw"></i>Regenerate
                                    </button>
                                </div>
                                <small class="text-muted">Hover to reveal token.</small>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Maximum disk quota</label>
                            <div class="col my-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="disk_max-quota" id="disk_max-quota_default" value="default" @if($user->settings()->get('disk.max_quota') == 'default') checked @endif @if(!Auth::user()->admin) disabled @endif>
                                    <label class="form-check-label" for="disk_max-quota_default">Default</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="disk_max-quota" id="disk_max-quota_custom" value="custom" @if($user->settings()->get('disk.max_quota') == 'custom') checked @endif @if(!Auth::user()->admin) disabled @endif>
                                    <label class="form-check-label" for="disk_max-quota_custom">Custom</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Custom disk quota</label>
                            <div class="col">
                                <div class="input-group">
                                    <input type="number" name="disk_custom-max-quota" id="disk_custom-max-quota" class="form-control" min="0" @if($user->settings()->get('disk.max_quota') == 'default' || !Auth::user()->admin) disabled @endif value="{{ $maxDiskQuota >> $maxDiskQuotaShift }}">
                                    <select name="disk_custom-max-quota_unit" id="disk_custom-max-quota_unit" class="form-select form-control" @if($user->settings()->get('disk.max_quota') == 'default' || !Auth::user()->admin) disabled @endif>
                                        @foreach(['bytes', 'KiB', 'MiB', 'GiB'] as $index => $unit)
                                            <option value="{{ $index * 10 }}" @if($maxDiskQuotaShift == $index * 10) selected @endif>{{ $unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <small class="m-0 text-muted">0 means no limit.</small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Auto-delete</label>
                            <div class="col my-auto">
                                <div class="pt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="disk_auto-delete" id="disk_auto-delete_enabled" value="1" @if($user->settings()->get('disk.auto_delete') == '1') checked @endif>
                                        <label class="form-check-label" for="disk_auto-delete_enabled">Enabled</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="disk_auto-delete" id="disk_auto-delete_disabled" value="0" @if($user->settings()->get('disk.auto_delete') == '0') checked @endif>
                                        <label class="form-check-label" for="disk_auto-delete_disabled">Disabled</label>
                                    </div>
                                </div>
                                <small class="m-0 text-muted">Older files will automatically be deleted when exceeding disk quota.</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-icon btn-outline-success"><i data-feather="save"></i>Save</button>
                    </form>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    Change password
                </div>
                <div class="card-body">
                    <form action="{{ $self ? route('user.settings.password') : route('admin.user.settings.password', ['userId' => $user->id]) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label for="oldPassword" class="col-sm-3 col-form-label">Old password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control @error('old_password') is-invalid @enderror" id="oldPassword" name="old_password" @if(!$self) disabled @endif>
                                @error('old_password')
                                    <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="newPassword" class="col-sm-3 col-form-label">New password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="newPassword" name="new_password">
                                @error('new_password')
                                    <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="confirmPassword" class="col-sm-3 col-form-label">Confirm password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" id="confirmPassword" name="new_password_confirmation">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-icon btn-outline-success"><i data-feather="save"></i>Save</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    Media displaying
                </div>
                <div class="card-body">
                    <form action="{{ $self ? route('user.settings.display') : route('admin.user.settings.display', ['userId' => $user->id]) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="image"></i>Pictures</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.image" id="1.default" value="default" autocomplete="off" @if($user->settings()->get('display.image') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="1.default">Default</label>
                                <input type="radio" class="btn-check" name="display.image" id="1.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.image') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="1.raw">Raw</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="video"></i>Videos</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.video" id="2.default" value="default" autocomplete="off" @if($user->settings()->get('display.video') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="2.default">Default</label>
                                <input type="radio" class="btn-check" name="display.video" id="2.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.video') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="2.raw">Raw</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="music"></i>Audios</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.audio" id="3.default" value="default" autocomplete="off" @if($user->settings()->get('display.audio') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="3.default">Default</label>
                                <input type="radio" class="btn-check" name="display.audio" id="3.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.audio') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="3.raw">Raw</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="file-text"></i>Text files</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.text" id="4.default" value="default" autocomplete="off" @if($user->settings()->get('display.text') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="4.default">Default</label>
                                <input type="radio" class="btn-check" name="display.text" id="4.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.text') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="4.raw">Raw</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="file-text"></i>PDF</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.pdf" id="5.default" value="default" autocomplete="off" @if($user->settings()->get('display.pdf') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="5.default">Default</label>
                                <input type="radio" class="btn-check" name="display.pdf" id="5.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.pdf') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="5.raw">Raw</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="archive"></i>Archives</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.zip" id="6.default" value="default" autocomplete="off" @if($user->settings()->get('display.zip') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="6.default">Default</label>
                                <input type="radio" class="btn-check" name="display.zip" id="6.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.zip') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="6.raw">Raw</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-6 col-form-label"><i data-feather="file"></i>Other files</label>
                            <div class="col-sm-6 text-end btn-group">
                                <input type="radio" class="btn-check" name="display.file" id="7.default" value="default" autocomplete="off" @if($user->settings()->get('display.file') == 'default') checked @endif>
                                <label class="btn btn-outline-secondary" for="7.default">Default</label>
                                <input type="radio" class="btn-check" name="display.file" id="7.raw" value="raw" autocomplete="off" @if($user->settings()->get('display.file') == 'raw') checked @endif>
                                <label class="btn btn-outline-secondary" for="7.raw">Raw</label>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary" data-action="set-display-default" >All default</button>
                        <button type="submit" class="btn btn-icon btn-outline-success"><i data-feather="save"></i>Save</button>
                    </form>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    ShareX configuration
                </div>
                <div class="card-body">
                    <p style="text-align: justify;">
                        ShareX is a free and open-source software to capture and upload screenshots and various files.
                        You can download it <a href="https://getsharex.com/">here</a>.
                    </p>
                    <div class="d-grid">
                        <button class="btn btn-icon btn-outline-{{ Cookie::get('theme', Setting::get('app.default_theme')) == 'dark' ? 'light' : 'dark' }}" @if($self) data-action="download-sharex" @else disabled @endif>
                            <i data-feather="download"></i>Download ShareX configuration
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
