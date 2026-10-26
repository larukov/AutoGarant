<footer id="footer-contacts" class="bg-secondary-dark text-white pt-5 pb-4 mt-auto border-t border-secondary">
    <div class="container">
        <div class="row g-4">
            <!-- Колонка 1: Описание проекта -->
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold text-primary mb-3">АВТОГАРАНТ</h5>
                <p class="small text-muted-light mb-2">
                    Клиент-серверное веб-приложение для автоматизации взаимодействия автосалона с клиентами и ведения
                    внутреннего учета продаж.
                </p>
            </div>

            <!-- Колонка 2: Быстрые ссылки -->
            <div class="col-lg-2 col-md-6 md-sm-none">
                <h6 class="text-uppercase fw-bold text-white mb-3">Разделы</h6>
                <ul class="list-unstyled small vstack gap-2">
                    <li><a href="{{ route('catalog.index') }}"
                            class="text-muted-light text-decoration-none hover-white">Каталог автомобилей</a></li>
                    <li><a href="{{ url('/#advantages') }}"
                            class="text-muted-light text-decoration-none hover-white">Кредитные программы</a></li>
                    <li><a href="{{ url('/#about') }}" class="text-muted-light text-decoration-none hover-white">История
                            компании</a></li>
                    <li><a href=""
                            class="text-muted-light text-decoration-none hover-white">Создать аккаунт</a></li>
                </ul>
            </div>

            <!-- Колонка 3: Контакты автосалона из ТЗ -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-uppercase fw-bold text-white mb-3">Контакты салона</h6>
                <ul class="list-unstyled small vstack gap-2 text-muted-light">
                    <li class="d-flex align-items-start gap-2">
                        <span>📍</span>
                        <span>Свердловская обл., <br>г. Каменск-Уральский</span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span>📞</span>
                        <a href="tel:+73439000000" class="text-muted-light text-decoration-none">+7 (3439) 00-00-00</a>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span>✉️</span>
                        <a href="mailto:info@ku-polytech.ru"
                            class="text-muted-light text-decoration-none">info@ku-polytech.ru</a>
                    </li>
                </ul>
            </div>

            <!-- Колонка 4: Исполнители (Студент / Руководитель) -->
            <div class="col-lg-3 col-md-6 border-start border-secondary ps-lg-4">
                <h6 class="text-uppercase fw-bold text-white mb-3">Курсовой проект</h6>
                <div class="small text-muted-light">
                    <p class="mb-2"><strong>Исполнитель:</strong><br> Ларюков Д.М., гр. ИСиП-23-401</p>
                    <p class="mb-0"><strong>Руководитель:</strong><br> Калмыкова И.М.</p>
                </div>
            </div>
        </div>

        <!-- Нижняя тонкая линия -->
        <div class="border-top border-secondary mt-4 pt-3 text-center">
            <p class="small text-muted mb-0" style="font-size: 0.75rem;">
                © 2026 автогарант
            </p>
        </div>
    </div>
</footer>