@props(['field', 'value', 'short' => false])

@php
    $map = [
        'transmission' => [
            'manual'    => $short ? 'Механика' : 'Механическая (МКПП)',
            'automatic' => $short ? 'Автомат' : 'Автоматическая (АКПП)',
            'robot'     => $short ? 'Робот' : 'Роботизированная',
        ],
        'drive_type' => [
            'FWD' => 'Передний (FWD)',
            'RWD' => 'Задний (RWD)',
            'AWD' => 'Полный (AWD)',
        ],
        'fuel_type' => [
            'petrol'   => 'Бензин',
            'diesel'   => 'Дизель',
            'electric' => 'Электро',
            'hybrid'   => 'Гибрид',
        ],
    ];
@endphp

{{ $map[$field][$value] ?? $value }}