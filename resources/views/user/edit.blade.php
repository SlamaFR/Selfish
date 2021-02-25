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
            <div class="card">
                <div class="card-header">
                    Profile summary
                </div>
                <div class="card-body">
                    <form action="{{ $self ? route('user.settings.info') : route('admin.user.settings.info', ['userId' => $user->id]) }}" method="POST">
                        @csrf
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">User code</label>
                            <div class="col-sm-9">
                                <input type="text" value="{{ $user->code }}" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Username</label>
                            <div class="col-sm-9">
                                <input name="username" type="text" value="{{ $user->username }}" class="form-control @error('username') is-invalid @enderror">
                                @error('username')
                                    <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Email address</label>
                            <div class="col-sm-9">
                                <input name="email" type="email" value="{{ $user->email }}" class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                    <span class="invalid-feedback text-start ms-1 mb-1" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Personnal token</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="password" class="form-control hover-to-see" value="{{ $user->access_token }}" id="personnal-token" readonly>
                                    <button class="btn btn-icon btn-outline-secondary copy" type="button" data-clipboard-target="#personnal-token">
                                        <i data-feather="clipboard"></i>Copy
                                    </button>
                                    <button class="btn btn-icon btn-outline-primary" type="button" id="regenerate-token">
                                        <i data-feather="refresh-cw"></i>Regenerate
                                    </button>
                                </div>
                                <small class="text-muted">Hover to reveal token.</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-icon btn-outline-success"><i data-feather="save"></i>Save</button>
                    </form>
                </div>
            </div>
            <div class="card mt-4">
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
                        <button class="btn btn-icon btn-outline-{{ Cookie::get('theme', 'dark') == 'dark' ? 'light' : 'dark' }}" @if($self) data-action="download-sharex" @else disabled @endif>
                            <i data-feather="download"></i>Download ShareX configuration
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
    const tokenUpdateMessage = "{{ $self ? "Your personnal access has been regenerated and copied to clipboard." : $user->username . "'s personnal access token has been regenerated and copied to clipboard." }}";
</script>
@endsection
