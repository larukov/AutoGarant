@props(['class' => ''])

@php
    $messages = [
        'success' => session('success'),
        'danger' => session('error'),
        'info' => session('info'),
    ];
@endphp

@foreach ($messages as $type => $msg)
    @if ($msg)
        <div class="alert alert-{{ $type }} alert-dismissible fade show {{ $class }}" role="alert">
            {{ $msg }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        </div>
    @endif
@endforeach
