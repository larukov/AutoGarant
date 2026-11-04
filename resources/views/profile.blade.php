@extends('layouts.app')

@section('title', 'Личный кабинет — Автосалон')

@section('content')
    <div class="container py-5">
        <div class="row g-4">
            <!-- Левая колонка: Профиль пользователя -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                    <div class="text-center mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 70px; height: 70px; font-size: 2rem;">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                    </div>
                    <h4 class="fw-bold text-center text-dark mb-1">{{ $user->name }}</h4>
                    <p class="text-muted text-center small mb-4">Зарегистрирован: {{ $user->created_at->format('d.m.Y') }}
                    </p>

                    <hr class="text-muted">

                    <div class="mb-3">
                        <span class="text-uppercase text-muted d-block small fw-bold"
                            style="font-size: 0.75rem;">Электронная почта</span>
                        <span class="text-dark fw-medium">{{ $user->email }}</span>
                    </div>
                    <div class="mb-0">
                        <span class="text-uppercase text-muted d-block small fw-bold" style="font-size: 0.75rem;">Номер
                            телефона</span>
                        <span class="text-dark fw-medium">{{ $user->tel }}</span>
                    </div>
                </div>
            </div>

            <!-- Правая колонка: Список бронирований и заказов -->
            <div class="col-12 col-md-8">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
                    <h3 class="fw-bold text-dark h4 mb-4 border-bottom pb-2">Мониторинг статуса исполнения договоров</h3>

                    @if (session('success'))
                        <x-alert class="mb-4" />
                    @endif

                    @if ($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small text-uppercase">
                                    <tr>
                                        <th scope="col" class="border-0">Заказ / Автомобиль</th>
                                        <th scope="col" class="border-0 text-center">Оплата</th>
                                        <th scope="col" class="border-0 text-center">Статус сделки</th>
                                        <th scope="col" class="border-0 text-end">Сумма</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <!-- Информация об авто -->
                                            <td class="py-3">
                                                <span class="fw-bold text-dark d-block">
                                                    {{ $order->car->model->brand->name }} {{ $order->car->model->name }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $order->car->year }} г.в. •
                                                    @if ($order->payment_type === 'cash')
                                                        Наличные
                                                    @elseif($order->payment_type === 'card')
                                                        Карта
                                                    @else
                                                        Автокредит
                                                    @endif
                                                </small>
                                                @if ($order->payment_type === 'credit' && $order->creditApplication)
                                                    <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                                        Взнос:
                                                        <strong>{{ number_format($order->creditApplication->down_payment, 0, ',', ' ') }}
                                                            ₽</strong> ·
                                                        {{ $order->creditApplication->loan_term_months }} мес ·
                                                        {{ number_format($order->creditApplication->interest_rate, 1, ',', ' ') }}%
                                                        · платёж
                                                        <strong>{{ number_format($order->creditApplication->monthly_payment, 0, ',', ' ') }}
                                                            ₽/мес</strong>
                                                    </div>
                                                @endif
                                                <div class="text-muted" style="font-size: 0.75rem;">от
                                                    {{ $order->created_at->format('d.m.Y в H:i') }}</div>
                                            </td>

                                            <!-- Статус оплаты (из ENUM 'payment_status') -->
                                            <td class="text-center py-3">
                                                <span
                                                    class="badge rounded-pill px-2 py-1 small 
                                                            @if ($order->payment_status === 'fully_paid') bg-success 
                                                            @elseif($order->payment_status === 'unpaid') bg-danger 
                                                            @else bg-info text-dark @endif">
                                                    @if ($order->payment_status === 'unpaid')
                                                        Не оплачен
                                                    @elseif($order->payment_status === '10%')
                                                        Предоплата 10%
                                                    @elseif($order->payment_status === '50%')
                                                        Предоплата 50%
                                                    @elseif($order->payment_status === 'fully_paid')
                                                        Оплачен полностью
                                                    @endif
                                                </span>
                                            </td>

                                            <!-- Динамический статус сделки (из ENUM 'status') по ТЗ -->
                                            <td class="text-center py-3">
                                                <span
                                                    class="badge py-2 px-3 fw-semibold
                                                            @if ($order->status === 'created') bg-light text-primary border border-primary
                                                            @elseif($order->status === 'confirmed') bg-warning text-dark
                                                            @elseif($order->status === 'rejected') bg-danger
                                                            @else bg-success @endif">

                                                    @if ($order->status === 'created')
                                                        Заявка оформлена
                                                    @elseif($order->status === 'confirmed')
                                                        Договор подписан
                                                    @elseif($order->status === 'rejected')
                                                        Сделка отклонена
                                                    @else
                                                        Сделка закрыта
                                                    @endif
                                                </span>
                                            </td>

                                            <!-- Финальная зафиксированная цена -->
                                            <td class="text-end py-3 fw-bold text-dark">
                                                <x-money :money="$order->final_price" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted mb-0">У вас пока нет оформленных заявок на бронирование или предзаказов.
                            </p>
                            <a href="{{ route('catalog.index') }}" class="btn btn-outline-primary btn-sm mt-3">Перейти в
                                каталог</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection