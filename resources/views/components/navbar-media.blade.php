<nav class="navbar navbar-expand-lg navbar-dark fixed-top px-4">
    <a class="navbar-brand" style="font-family: Pacifico" href="{{ route('home') }}">Selfish</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto"></ul>

        <ul class="navbar-nav d-flex">
            <li class="nav-item">
                @php
                $currentMode = Cookie::get('theme', 'dark');
                $nextMode = $currentMode == 'dark' ? 'light' : 'dark';
                @endphp
                <form action="{{ route('mode', ['mode' => $nextMode]) }}" method="post" id="form">
                    @csrf
                    <a class="nav-link" onclick="$('#form').submit()" style="cursor: pointer;">
                        <i data-feather="{{ $currentMode == "dark" ? "sun" : "moon" }}"></i>
                    </a>
                </form>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link copy" data-clipboard-text={{ route('media.view', ['userCode' => $user_code, 'mediaCode' => $media_code]) }}>
                    <i data-feather="link"></i>
                </a>
            </li>
            <li class="nav-item">
                <a target="_blank" href="{{ route('media.download', ['userCode' => $user_code, 'mediaCode' => $media_code]) }}" class="nav-link">
                    <i data-feather="download"></i>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('media.raw', ['userCode' => $user_code, 'mediaCode' => $media_code]) }}" class="nav-link">
                    <i data-feather="file-text"></i>
                </a>
            </li>
            <li class="nav-item" data-id="{{ $file->id }}" data-visible="{{ $file->visible() }}">
                <a href="#" data-action="toggle-visibility" class="nav-link">
                    <i data-feather="{{ $file->visible() ? "eye-off" : "eye"}}"></i>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i data-feather="trash-2"></i>
                </a>
            </li>
        </ul>
    </div>
  </nav>
<div class="mb-3" style="height: 58px;"></div>