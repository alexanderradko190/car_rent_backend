<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Договор аренды автомобиля</title>
    <style>body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }</style>
</head>
<body>
<h2>Договор аренды автомобиля</h2>

<p>Клиент: {{ $client->full_name }}</p>
<p>Автомобиль: {{ $car->make }} {{ $car->model }} (VIN: {{ $car->vin }})</p>
<p>Срок аренды: {{ $request->start_time }} — {{ $request->end_time }}</p>
<p>Стоимость аренды: {{ $request->total_cost }} руб.</p>
<p>Опция страхования: {{ $request->insurance_option ? 'Да' : 'Нет' }}</p>
<br>
<p>ООО "Арендодатель" и Клиент заключили договор аренды автомобиля на вышеуказанных условиях.</p>
</body>
</html>
