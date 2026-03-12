@php
    use Illuminate\Support\Carbon;

    $contractId = $request->id;

    $contractDate = $request->created_at
        ? Carbon::parse($request->created_at)->format('d.m.Y')
        : Carbon::now()->format('d.m.Y');

    $startAt = $request->start_time ? Carbon::parse($request->start_time) : null;
    $endAt   = $request->end_time ? Carbon::parse($request->end_time) : null;

    $startTime = $startAt ? $startAt->format('H:i') : '';
    $endTime   = $endAt ? $endAt->format('H:i') : '';

    $durationMinutes = ($startAt && $endAt && $endAt->greaterThanOrEqualTo($startAt))
        ? $startAt->diffInMinutes($endAt)
        : null;

    $carTitle = trim(($car->make ?? '') . ' ' . ($car->model ?? ''));
@endphp
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .block { margin: 0 0 10px; }
    </style>
</head>
<body>
    <p class="block">Договор аренды № {{ $contractId }}</p>
    <p class="block">Арендодатель: ООО Автомобильные системы</p>
    <p class="block">Реквизиты счета:</p>
    <p class="block">Арендатор: {{ $client->full_name ?? '' }}</p>
    <p class="block">Дата заключения договора: {{ $contractDate }}</p>
    <p class="block">Начало аренды: {{ $startTime }}</p>
    <p class="block">Окончание аренды: {{ $endTime }}</p>
    <p class="block">Автомобиль: {{ $carTitle }}</p>
    <p class="block">Класс автомобиля: {{ $car->car_class?->label() ?? $car->car_class ?? '' }}</p>
    <p class="block">Продолжительность аренды (мин): {{ $durationMinutes ?? '' }}</p>
    <p class="block">Общая стоимость аренды: {{ $request->total_cost ?? '' }}</p>
</body>
</html>
