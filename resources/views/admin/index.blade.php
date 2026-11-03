@extends('layouts.app')

@section('title', 'Панель администратора (CRM) — Автосалон')

@section('content')
    <div class="container-fluid py-5 px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold h2 text-dark">Панель администратора</h1>
            <a href="{{ route('admin.cars.index') }}" class="btn btn-primary fw-bold">
                Управление каталогом автомобилей
            </a>
        </div>

        <div class="row g-3 mb-5">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-primary text-white">
                    <span class="text-uppercase small fw-bold d-block mb-1" style="opacity: 0.8;">Выручка по закрытым
                        сделкам</span>
                    <h3 class="display-6 fw-bold mb-0"><x-money :money="$totalSales" /></h3>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 p-4 bg-success text-white">
                    <span class="text-uppercase small fw-bold d-block mb-1" style="opacity: 0.8;">Успешно подписано
                        договоров</span>
                    <h3 class="display-6 fw-bold mb-0">{{ $closedDealsCount }} шт.</h3>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3 p-4 bg-white">
            <h3 class="fw-bold text-dark h4 mb-4 border-bottom pb-2">Все поступающие заявки и статусы сделок</h3>

            @if(session('success'))
                <x-alert class="mb-4" />
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary small text-uppercase">
                        <tr>
                            <th>ID / Дата</th>
                            <th>Клиент (ФИО / Контакты)</th>
                            <th>Автомобиль</th>
                            <th>Тип оплаты</th>
                            <th>Статус оплаты</th>
                            <th>Статус сделки</th>
                            <th class="text-center">Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">#{{ $order->id }}</span>
                                    <div class="text-muted small">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $order->user->name }}</span>
                                    <small class="text-muted">{{ $order->user->email }} •
                                        {{ $order->user->tel }}</small>
                                </td>

                                <td>
                                    <span class="fw-bold text-dark d-block">{{ $order->car->model->brand->name }}
                                        {{ $order->car->model->name }}</span>
                                    <small class="text-primary fw-medium"><x-money :money="$order->final_price" /></small>
                                </td>

                                <td>
                                    @if($order->payment_type === 'cash') <span class="badge bg-secondary">Наличные</span>
                                    @elseif($order->payment_type === 'card') <span class="badge bg-secondary">Карта</span>
                                    @else <span class="badge bg-info text-dark">Автокредит</span> @endif
                                </td>
                                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                                    @csrf
                                    <td>
                                        <x-ui.select name="payment_status" :options="[
                                            'unpaid' => 'Не оплачен',
                                            '10%' => 'Предоплата 10%',
                                            '50%' => 'Предоплата 50%',
                                            'fully_paid' => 'Оплачен полностью',
                                        ]" :value="$order->payment_status" :use-old="false" class="form-select-sm"
                                            wrapperClass="mb-0" style="min-width: 140px;" />
                                    </td>

                                    <td>
                                        <x-ui.select name="status" :options="[
                                            'created' => 'Заявка оформлена',
                                            'confirmed' => 'Договор подписан',
                                            'rejected' => 'Сделка отклонена',
                                        ]" :value="$order->status" :use-old="false"
                                            :class="'form-select-sm fw-semibold ' . ($order->status === 'confirmed' ? 'text-success' : ($order->status === 'rejected' ? 'text-danger' : 'text-primary'))"
                                            wrapperClass="mb-0" style="min-width: 160px;" />
                                    </td>

                                    <!-- Кнопка сохранения изменений в строке -->
                                    <td class="text-center">
                                        <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold">
                                            Обновить
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection