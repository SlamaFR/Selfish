<nav class="navbar navbar-expand-lg navbar-dark fixed-top px-4">
    <a class="navbar-brand" style="font-family: Pacifico" href="{{ route('home') }}">Selfish</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto"></ul>

        <ul class="navbar-nav d-flex">
            <li class="nav-item">
                <a class="nav-link" data-action="toggle-dark-mode">
                    <i data-feather="{{ Cookie::get('theme', Setting::get('app.default_theme')) == "dark" ? "sun" : "moon" }}"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link copy" data-clipboard-text={{ route('media.view', ['mediaCode' => $media_code]) }}>
                    <i data-feather="link"></i>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('media.download', ['mediaCode' => $media_code]) }}" class="nav-link">
                    <i data-feather="download"></i>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('media.raw', ['mediaCode' => $media_code]) }}" class="nav-link">
                    <i data-feather="file-text"></i>
                </a>
            </li>
            @if(!Auth::guest() && (Auth::user()->code == $user_code || Auth::user()->admin))
                <li class="nav-item" data-id="{{ $file->media_code }}" data-name="{{ $file->media_name }}" data-visible="{{ $file->visible }}">
                    <a data-action="toggle-visibility" class="nav-link">
                        <i data-feather="{{ $file->visible ? "eye-off" : "eye"}}"></i>
                    </a>
                </li>
                <li class="nav-item" data-id="{{ $file->media_code }}" >
                    <a data-action="delete-media-confirm" class="nav-link">
                        <i data-feather="trash-2"></i>
                    </a>
                </li>
            @endif
        </ul>
    </div>
  </nav>
<div class="mb-3" style="height: 58px;"></div>