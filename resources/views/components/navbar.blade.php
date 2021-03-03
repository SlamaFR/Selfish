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
                    <a class="nav-link @if(str_starts_with(Route::currentRouteName(), $route)) active @endif" href="{{ route($route) }}">
                        <i data-feather="{{ $meta['icon'] }}"></i>@lang('navbar.' . $route)
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
                    <ul class="dropdown-menu dropdown-menu-end" style="width: 220px">
                        <li class="pt-2 px-3">
                            @php($quota = Auth::user()->disk_quota)
                            @php($maxQuota = Auth::user()->getEffectiveMaxDiskQuota())
                            @php($ratio = $maxQuota > 0 ? $quota / $maxQuota : 0)
                            <div class="progress position-relative">
                                <div class="progress-bar @if($ratio < .6) bg-success @elseif($ratio < .85) bg-warning @else bg-danger @endif" role="progressbar" style="width: {{ $ratio * 100 }}%" id="navbar_quota_progress"></div>
                            </div>
                            <small class="text-muted m-0 d-flex pt-1 justify-content-center" id="navbar_quota_caption">
                                @if($maxQuota == 0)
                                @lang('navbar.storage.unlimited')
                                @else
                                @lang('navbar.storage.usage', ['usage' => Files::humanFileSize($quota), 'max' => Files::humanFileSize($maxQuota)])
                                @endif
                            </small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item @if(Route::currentRouteName() == "user.settings") active @endif" href="{{ route('user.settings') }}"><i data-feather="settings"></i>@lang('navbar.settings')</a></li>
                        <li>
                            <button class="dropdown-item" data-action="toggle-dark-mode-text">
                                <i data-feather="{{ Cookie::get('theme', Setting::get('app.default_theme')) == "dark" ? "sun" : "moon" }}"></i>@lang('navbar.theme.' . Cookie::get('theme', Setting::get('app.default_theme')))
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}"><i data-feather="log-out"></i>@lang('navbar.logout')</a></li>
                    </ul>
                </li>
            </ul>
        </div>
      </div>
  </nav>
<div class="mb-3" style="height: 58px;"></div>