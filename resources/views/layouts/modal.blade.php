<!-- Modal авторизации -->
<div class="modal fade" id="AuthorizationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Авторизация</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('login') }}" method="POST" class="d-flex flex-column">
                    @csrf

                    {{-- Вывод ошибок --}}
                    <x-validation-errors bag="login" />

                    <x-ui.input name="email" type="email" label="Email" placeholder="example@mail.ru" required />
                    <x-ui.input name="password" type="password" label="Пароль" required />

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                    </div>

                    <a href="#" class="text-center mb-2"
                        onclick="switchModal('AuthorizationModal','RegModal'); return false;">
                        Нет аккаунта? Зарегистрироваться
                    </a>

                    <button type="submit" class="btn btn-primary mt-2">Авторизоваться</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal регистрации -->
<div class="modal fade" id="RegModal" tabindex="-1" aria-labelledby="exampleModallabel" aria-hidden="true">
    <!-- modal-lg делает модальное окно шире на десктопах -->
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModallabel">Регистрация</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('register') }}" method="POST" class="d-flex flex-column">
                    @csrf

                    <x-validation-errors />

                    <!-- Основные данные: Имя и Email -->
                    <div class="row mb-2">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <x-ui.input name="name" type="text" label="Имя" placeholder="Иван" required />
                        </div>
                        <div class="col-12 col-md-6">
                            <x-ui.input name="email" type="email" label="Email" placeholder="example@mail.ru" required />
                        </div>
                    </div>

                    <!-- Телефон ( name="phone" в соответствии с полем в БД ) -->
                    <div class="row mb-2">
                        <div class="col-12">
                            <x-ui.input name="phone" type="tel" id="phone" label="Номер телефона" placeholder="+7 (999) 123-45-67" required/>
                        </div>
                    </div>

                    <!-- Безопасность: Пароли -->
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <x-ui.input name="password" type="password" label="Пароль" required />
                        </div>
                        <div class="col-12 col-md-6">
                            <x-ui.input name="password_confirmation" type="password" label="Подтверждение пароля" required />
                        </div>
                    </div>

                    <a href="#" class="text-center mb-3"
                        onclick="switchModal('RegModal','AuthorizationModal'); return false;">
                        Есть аккаунт? Войти
                    </a>

                    <button type="submit" class="btn btn-primary mt-2">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>
</div>
