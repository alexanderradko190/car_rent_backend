@php
    use Illuminate\Support\Carbon;

    $contractId = $rentHistory->id;

    $contractDate = $rentHistory->created_at
        ? Carbon::parse($rentHistory->created_at)->format('d.m.Y')
        : Carbon::now()->format('d.m.Y');

    $startAt = $rentHistory->start_time ? Carbon::parse($rentHistory->start_time) : null;
    $endAt   = $rentHistory->end_time ? Carbon::parse($rentHistory->end_time) : null;

    $startTime = $startAt ? $startAt->format('H:i') : '';
    $endTime   = $endAt ? $endAt->format('H:i') : '';

    $durationMinutes = ($startAt && $endAt && $endAt->greaterThanOrEqualTo($startAt))
        ? $startAt->diffInMinutes($endAt)
        : null;

$carTitle = trim(($car->make ?? '') . ' ' . ($car->model ?? ''));
@endphp

<p>Договор аренды № {{ $contractId }}</p>

<p>Арендодатель: ООО Автомобильные системы</p>

<p>Реквизиты счета:</p>

<p>Арендатор: {{ $client->full_name ?? '' }}</p>

<p>Дата заключения договора: {{ $contractDate }}</p>

<p>
    Начало аренды: {{ $startTime }}<br>
    Окончание аренды: {{ $endTime }}
</p>

<p>
    Автомобиль: {{ $carTitle }}<br>
    Класс автомобиля: {{ $car->car_class?->label() ?? $car->car_class ?? '' }}
</p>

<p>Продолжительность аренды (мин): {{ $durationMinutes ?? '' }}</p>

<p>Общая стоимость аренды: {{ $rentHistory->total_cost ?? '' }} руб</p>
