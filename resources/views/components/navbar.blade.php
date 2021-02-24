<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container" style="max-width: 1140px;">
        <a class="navbar-brand" style="font-family: Pacifico">Selfish</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                @foreach(Config::get('pages') as $route => $meta)
                    @if(!$meta['admin'] || ($meta['admin'] && Auth::user()->admin))
                    <li class="nav-item">
                        <a class="nav-link @if(Route::currentRouteName() == $route) active @endif" href="{{ route($route) }}">
                            <i data-feather="{{ $meta['icon'] }}"></i>@lang('pages.' . $route)
                        </a>
                    </li>
                    @endif
                @endforeach
            </ul>

            <ul class="navbar-nav d-flex">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle @if(str_starts_with(Route::currentRouteName(), "user")) active @endif" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i data-feather="user"></i>{{ Auth::user()->username }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                      <li><a class="dropdown-item @if(Route::currentRouteName() == "user.settings") active @endif" href="{{ route('user.settings') }}"><i data-feather="settings"></i>Settings</a></li>
                      <li>
                        <button class="dropdown-item" data-action="toggle-dark-mode-text">
                            <i data-feather="{{ Cookie::get('theme', 'dark') == "dark" ? "sun" : "moon" }}"></i>{{ Cookie::get('theme', 'dark') == "dark" ? "Light" : "Dark" }} mode
                        </button>
                      </li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item" href="{{ route('logout') }}"><i data-feather="log-out"></i>Log out</a></li>
                    </ul>
                </li>
            </ul>
        </div>
      </div>
  </nav>
<div class="mb-3" style="height: 58px;"></div>