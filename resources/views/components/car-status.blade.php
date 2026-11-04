@props(['car', 'compact' => false])

<span
    class="badge {{ $compact ? 'py-1 px-2' : 'py-2 px-3 fw-bold' }} @if ($car->status === 'available') bg-success @elseif($car->status === 'reserved') bg-warning text-dark @else bg-danger @endif">
    @if ($car->status === 'available')
        {{ $compact ? 'В наличии' : 'В наличии на складе' }}
    @elseif($car->status === 'reserved')
        {{ $compact ? 'Бронь' : 'Забронирован' }}
    @else
        Продан
    @endif
</span>
