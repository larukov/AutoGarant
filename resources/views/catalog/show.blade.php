@extends('layouts.app')

@section('title', $car->model->brand->name . ' ' . $car->model->name . ' (' . $car->year . ' г.в.) — Автосалон')

@section('content')
    <div class="container py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}" class="text-decoration-none">Каталог</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $car->model->brand->name }}
                    {{ $car->model->name }}
                </li>
            </ol>
        </nav>

        @if (session('success'))
            <x-alert class="mb-4" />
        @endif

        <div class="row g-4">
            <div class="col-12 col-lg-7">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white p-3">
                    <div class="main-image-container bg-light rounded mb-3" style="height: 450px;">
                        <img src="{{ $car->images->first()->image_url ?? '/images/default-car.jpg' }}" id="mainCarImage"
                            alt="{{ $car->model->brand->name }} {{ $car->model->name }}"
                            class="w-100 h-100 object-cover rounded">
                    </div>

                    @if ($car->images->count() > 1)
                        <div class="row g-2 overflow-auto flex-nowrap px-1" style="max-height: 100px;">
                            @foreach ($car->images as $image)
                                <div class="col-3" style="cursor: pointer; min-width: 100px;">
                                    <img src="{{ $image->image_url }}" alt="Миниатюра"
                                        class="img-thumbnail w-100 h-100 object-cover"
                                        onclick="document.getElementById('mainCarImage').src = this.src">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Правая колонка: Информация, Цена и Кнопка бронирования -->
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white sticky-lg-top" style="top: 2rem; z-index: 10;">

                    <!-- Статус авто (из ENUM поля 'status') -->
                    <div class="mb-2">
                        <x-car-status :car="$car" />
                    </div>

                    <!-- Название марки и модели -->
                    <h1 class="fw-bold text-dark display-6 mb-1">
                        {{ $car->model->brand->name }} {{ $car->model->name }}
                    </h1>
                    <p class="text-muted fs-5 mb-4">{{ $car->bodyType->name }} • {{ $car->year }} год выпуска</p>

                    <!-- Фиксация цены -->
                    <div class="p-3 bg-light rounded-3 mb-4 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase text-muted small fw-bold d-block"
                                style="font-size: 0.75rem; letter-spacing: 0.5px;">Актуальная стоимость</span>
                            <span class="display-6 fw-bold text-dark"><x-money :money="$car->price" /></span>
                        </div>
                        <span class="text-success small fw-semibold">Цена зафиксирована</span>
                    </div>

                    <!-- Кнопка вызова формы резервирования / Условия доступности -->
                    @if ($car->status === 'available')
                        @auth
                            <button type="button" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm rounded-3 mb-3"
                                data-bs-toggle="modal" data-bs-target="#orderReservationModal">
                                Зарезервировать онлайн
                            </button>
                        @else
                            <button type="button" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm rounded-3 mb-3"
                                data-bs-toggle="modal" data-bs-target="#AuthorizationModal">
                                Войти для бронирования
                            </button>
                            <p class="text-muted text-center small mb-0">Онлайн-резервирование доступно только авторизованным
                                клиентам.</p>
                        @endauth
                    @else
                        <button class="btn btn-secondary btn-lg w-100 py-3 fw-bold rounded-3 mb-3" disabled>
                            Автомобиль недоступен
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Блок подробных характеристик и комплектации -->
        <div class="row mt-5">
            <div class="col-12 col-lg-7">
                <!-- Таблица технических характеристик -->
                <div class="bg-white p-4 rounded-3 shadow-sm mb-4">
                    <h3 class="fw-bold text-dark mb-4 h4 border-bottom pb-2">Детальные технические характеристики</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-borderless align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted py-2 w-50">Год выпуска</td>
                                    <td class="fw-bold text-dark py-2">{{ $car->year }} г.</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-2">Тип кузова</td>
                                    <td class="fw-bold text-dark py-2">{{ $car->bodyType->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-2">Объем двигателя</td>
                                    <td class="fw-bold text-dark py-2">{{ number_format($car->engine_volume, 1) }} л.</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-2">Коробка передач (КПП)</td>
                                    <td class="fw-bold text-dark py-2">
                                        <x-spec-text field="transmission" :value="$car->transmission" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-2">Тип привода</td>
                                    <td class="fw-bold text-dark py-2">
                                        <x-spec-text field="drive_type" :value="$car->drive_type" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-2">Тип топлива</td>
                                    <td class="fw-bold text-dark py-2">
                                        <x-spec-text field="fuel_type" :value="$car->fuel_type" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Блок Дополнительных опций (Многие-ко-многим `options`) -->
                <div class="bg-white p-4 rounded-3 shadow-sm">
                    <h3 class="fw-bold text-dark mb-3 h4 border-bottom pb-2">Установленные дополнительные опции</h3>
                    @if ($car->options && $car->options->count() > 0)
                        <div class="row g-3 pt-2">
                            @foreach ($car->options as $option)
                                <div class="col-12 col-md-6 d-flex align-items-center">
                                    <div class="text-success me-2">✓</div>
                                    <div>
                                        <span class="text-dark d-block fw-semibold">{{ $option->name }}</span>
                                        @if ($option->price > 0)
                                            <small class="text-muted">+ <x-money :money="$option->price" /></small>
                                        @else
                                            <small class="text-success">Включено в стоимость</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0 pt-2">Дополнительные опции для данного автомобиля не указаны. Комплектация
                            базовая.</p>
                    @endif
                </div>
                <!-- Интерактивный калькулятор автокредита -->
                <div class="bg-white p-4 rounded-3 shadow-sm mt-4" id="creditCalculator"
                    data-car-price="{{ $car->price }}">
                    <h3 class="fw-bold text-dark mb-4 h4 border-bottom pb-2">📊 Предварительный расчет автокредита</h3>

                    <div class="row g-3">
                        <!-- Слайдер: Первоначальный взнос -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="downPaymentRange" class="form-label text-muted small mb-0">Первоначальный
                                    взнос</label>
                                <span class="fw-bold text-dark" id="downPaymentValue">0 ₽</span>
                            </div>
                            <!-- value="0" — минимальный взнос, max равен стоимости авто -->
                            <input type="range" class="form-range" id="downPaymentRange" min="0"
                                max="{{ $car->price }}" step="50000" value="0">
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                                <span>0%</span>
                                <span>50%</span>
                                <span>100%</span>
                            </div>
                        </div>

                        <!-- Слайдер: Срок кредитования -->
                        <div class="col-12 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="periodRange" class="form-label text-muted small mb-0">Срок
                                    кредитования</label>
                                <span class="fw-bold text-dark" id="periodValue">36 мес.</span>
                            </div>
                            <input type="range" class="form-range" id="periodRange" min="12" max="84"
                                step="12" value="36">
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                                <span>1 год</span>
                                <span>3 года</span>
                                <span>7 лет</span>
                            </div>
                        </div>

                        <!-- Скрытая базовая ставка из БД / ТЗ -->
                        <input type="hidden" id="interestRate" value="11.5">

                        <!-- Вывод результатов расчета -->
                        <div class="col-12 mt-4">
                            <div
                                class="p-3 bg-light rounded-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                                <div class="mb-2 mb-sm-0">
                                    <span class="text-muted small d-block">Ежемесячный платеж (ориентировочно):</span>
                                    <span class="display-6 fw-bold text-primary" id="monthlyPaymentValue">0 ₽</span>
                                </div>
                                <div class="text-sm-end">
                                    <span class="text-muted small d-block">Процентная ставка:</span>
                                    <span class="fw-bold text-success fs-5">11.5% годовых</span>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                                * Расчет является предварительным. Точные условия будут зафиксированы в договоре
                                купли-продажи менеджером автосалона.
                            </small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @auth
        <div class="modal fade" id="orderReservationModal" tabindex="-1" aria-labelledby="orderModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="orderModalLabel">Онлайн-резервирование</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="car_id" value="{{ $car->id }}">

                        <div class="modal-body">
                            <p class="text-muted small mb-3">
                                Вы резервируете автомобиль <strong>{{ $car->model->brand->name }}
                                    {{ $car->model->name }}</strong> со склада автосалона.
                            </p>

                            <x-ui.select name="payment_type" label="Способ оплаты" id="payment_type" :options="[
                                'cash' => 'Наличный расчет в автосалоне',
                                'card' => 'Банковская карта',
                                'credit' => 'Оформление в автокредит',
                            ]"
                                :required="true" labelClass="form-label small fw-bold text-secondary" />

                            <!-- Наглядный блок расчёта автокредита в модалке -->
                            <div id="creditSummaryBlock" class="d-none border rounded-3 p-3 mb-3 bg-white">
                                <h6 class="fw-bold text-dark mb-3">
                                    <span class="me-1">🏦</span> Условия автокредита
                                </h6>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label small text-muted mb-0">Первоначальный взнос</label>
                                        <span class="fw-bold text-dark small" id="modalDownPaymentValue">0 ₽</span>
                                    </div>
                                    <input type="range" class="form-range" id="modalDownPaymentRange" min="0"
                                        max="{{ $car->price }}" step="50000" value="0">
                                    <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                                        <span>0%</span><span>50%</span><span>100%</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label small text-muted mb-0">Срок кредита</label>
                                        <span class="fw-bold text-dark small" id="modalPeriodValue">36 мес.</span>
                                    </div>
                                    <input type="range" class="form-range" id="modalPeriodRange" min="12"
                                        max="84" step="12" value="36">
                                    <div class="d-flex justify-content-between text-muted" style="font-size: 0.72rem;">
                                        <span>1 год</span><span>4 года</span><span>7 лет</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                    <span class="small text-muted">Процентная ставка</span>
                                    <span class="fw-bold text-success small">11.5% годовых</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                    <span class="small text-muted">Ежемесячный платёж</span>
                                    <span class="fw-bold text-primary fs-5" id="modalMonthlyPaymentValue">0 ₽</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="small text-muted">Итого по кредиту</span>
                                    <span class="fw-bold text-dark" id="modalTotalLoanValue">0 ₽</span>
                                </div>
                                <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">
                                    * Предварительный расчёт. Точные условия фиксирует менеджер автосалона.
                                </small>
                            </div>

                            @if ($car->options && $car->options->count() > 0)
                                <hr>
                                <h6 class="fw-bold text-dark small mb-2">Дополнительные опции</h6>
                                <div class="mb-3">
                                    @foreach ($car->options as $option)
                                        <div class="form-check d-flex justify-content-between align-items-center border rounded p-2 mb-1 option-row"
                                            data-price="{{ $option->price }}">
                                            <div>
                                            <x-ui.checkbox name="options[]" value="{{ $option->id }}"
                                                id="option{{ $option->id }}" />
                                            <label class="form-check-label small ms-1" for="option{{ $option->id }}">
                                                {{ $option->name }}
                                            </label>
                                        </div>
                                            <span class="small text-muted" style="font-size: 0.8rem;">
                                                {{ $option->price > 0 ? '+' . number_format($option->price, 0, ',', ' ') . ' ₽' : 'Включено' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            
                            <input type="hidden" name="down_payment" id="orderDownPayment" value="0">
                            <input type="hidden" name="loan_term_months" id="orderLoanTerm" value="36">
                            <input type="hidden" name="interest_rate" id="orderInterestRate" value="11.5">
                            <input type="hidden" name="final_price" id="orderFinalPrice" value="{{ $car->price }}">

                            <div class="p-3 bg-light rounded-3 mb-2">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small text-muted">Базовая стоимость:</span>
                                    <span class="text-dark fw-semibold small"
                                        id="summaryBasePrice">{{ number_format($car->price, 0, ',', ' ') }} ₽</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small text-muted">Доп. опции:</span>
                                    <span class="text-dark fw-semibold small" id="summaryOptionsPrice">0 ₽</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="small fw-bold text-dark">Итого:</span>
                                    <span class="fw-bold text-dark"
                                        id="summaryTotalPrice">{{ number_format($car->price, 0, ',', ' ') }} ₽</span>
                                </div>
                            </div>

                            <div class="p-3 bg-light rounded-3 mb-2">
                                <div class="d-flex justify-content-between mb-1 small text-muted">
                                    <span>Имя клиента:</span>
                                    <span class="text-dark fw-semibold">{{ Auth::user()->name }}</span>
                                </div>
                                <div class="d-flex justify-content-between small text-muted">
                                    <span>Телефон для связи:</span>
                                    <span class="text-dark fw-semibold">{{ Auth::user()->tel }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                            <button type="submit" class="btn btn-primary fw-bold">Подтвердить резерв</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endauth
@endsection
@section('script')
    <script src="{{ asset('js/calculator.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const basePrice = parseFloat('{{ $car->price }}') || 0;
            const optionsPriceEl = document.getElementById('summaryOptionsPrice');
            const totalPriceEl = document.getElementById('summaryTotalPrice');
            const finalPriceInput = document.getElementById('orderFinalPrice');
            const optionRows = document.querySelectorAll('.option-row');
            const optionCheckboxes = document.querySelectorAll('input[name="options[]"]');

            // --- Элементы кредитного блока в модалке ---
            const paymentType = document.getElementById('payment_type');
            const creditBlock = document.getElementById('creditSummaryBlock');
            const modalDownRange = document.getElementById('modalDownPaymentRange');
            const modalPeriodRange = document.getElementById('modalPeriodRange');
            const modalDownValue = document.getElementById('modalDownPaymentValue');
            const modalPeriodValue = document.getElementById('modalPeriodValue');
            const modalMonthly = document.getElementById('modalMonthlyPaymentValue');
            const modalTotalLoan = document.getElementById('modalTotalLoanValue');

            // Скрытые поля, которые уходят в заказ
            const orderDown = document.getElementById('orderDownPayment');
            const orderLoanTerm = document.getElementById('orderLoanTerm');
            const orderInterest = document.getElementById('orderInterestRate');

            const INTEREST_RATE = 11.5;

            function fmt(v) {
                return Math.round(v).toLocaleString('ru-RU') + ' ₽';
            }

            function getCurrentTotal() {
                let optionsTotal = 0;
                optionRows.forEach(function(row) {
                    const cb = row.querySelector('input[type="checkbox"]');
                    if (cb && cb.checked) optionsTotal += parseFloat(row.dataset.price) || 0;
                });
                return basePrice + optionsTotal;
            }

            // Пересчёт кредитного блока от текущей итоговой цены
            function recalcCredit() {
                if (!creditBlock) return;
                const total = getCurrentTotal();
                const down = parseFloat(modalDownRange.value) || 0;
                const months = parseInt(modalPeriodRange.value) || 0;
                const loanAmount = total - down;

                modalDownRange.max = total;
                modalDownValue.innerText = fmt(down);
                modalPeriodValue.innerText = months + ' мес.';

                let monthly = 0;
                let totalLoan = 0;
                if (loanAmount > 0 && months > 0) {
                    const mr = INTEREST_RATE / 12 / 100;
                    const coeff = (mr * Math.pow(1 + mr, months)) / (Math.pow(1 + mr, months) - 1);
                    monthly = loanAmount * coeff;
                    totalLoan = down + monthly * months;
                }
                modalMonthly.innerText = fmt(monthly);
                modalTotalLoan.innerText = fmt(totalLoan);

                // Синхронизируем скрытые поля со значениями из модалки
                if (orderDown) orderDown.value = down;
                if (orderLoanTerm) orderLoanTerm.value = months;
                if (orderInterest) orderInterest.value = INTEREST_RATE;
            }

            function recalc() {
                const total = getCurrentTotal();
                const optionsTotal = total - basePrice;
                if (optionsPriceEl) optionsPriceEl.innerText = fmt(optionsTotal);
                if (totalPriceEl) totalPriceEl.innerText = fmt(total);
                if (finalPriceInput) finalPriceInput.value = total;

                // Обновляем цену кредитного калькулятора
                if (window.creditCalculator) {
                    window.creditCalculator.price = total;
                }
                recalcCredit();
            }

            // Показ/скрытие кредитного блока при выборе способа оплаты
            function toggleCreditBlock() {
                if (!creditBlock) return;
                if (paymentType && paymentType.value === 'credit') {
                    creditBlock.classList.remove('d-none');
                    recalcCredit();
                } else {
                    creditBlock.classList.add('d-none');
                }
            }

            if (paymentType) paymentType.addEventListener('change', toggleCreditBlock);
            if (modalDownRange) modalDownRange.addEventListener('input', recalcCredit);
            if (modalPeriodRange) modalPeriodRange.addEventListener('input', recalcCredit);

            optionCheckboxes.forEach(function(cb) {
                cb.addEventListener('change', recalc);
            });

            toggleCreditBlock();
        });
    </script>
@endsection
