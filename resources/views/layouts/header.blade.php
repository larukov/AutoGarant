<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary py-3 shadow-sm">
        <div class="container">
            <!-- Логотип автосалона -->
            <a class="navbar-brand fw-bold tracking-wider" href="{{ url('/') }}">
                <span class="text-primary">АВТО</span>ГАРАНТ
            </a>

            <!-- Кнопка бургер-меню для мобильных устройств -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Переключить навигацию">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Ссылки на основные разделы согласно ТЗ -->
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('/') ? 'active fw-bold' : '' }}"
                            href="{{ url('/') }}">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('catalog*') ? 'active fw-bold' : '' }}"
                            href="{{ route('catalog.index') }}">Каталог</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/#advantages') }}">Кредит</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/#about') }}">О компании</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#footer-contacts">Контакты</a>
                    </li>
                </ul>

                <!-- Блок авторизации / Личного кабинета -->
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ route('profile.index') }}" class="btn btn-outline-primary btn-sm px-3 rounded-2">
                            Личный кабинет
                        </a>
                        @if(Auth::user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-sm px-3 rounded-2">
                                Админ-панель
                            </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link text-muted text-decoration-none btn-sm">Выйти</button>
                        </form>
                    @endauth

                    @guest
                        <button class="btn btn-outline-light btn-sm px-3 rounded-2" data-bs-toggle="modal" data-bs-target="#AuthorizationModal">Вход</button>
                        <button class="btn btn-primary btn-sm px-3 rounded-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#RegModal">Регистрация</button>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
</header>