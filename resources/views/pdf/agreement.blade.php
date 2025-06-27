<!DOCTYPE html>
<html>
<head>
    <title>Договор аренды</title>
</head>
<body>
<h1>Договор аренды автомобиля</h1>
<p>Клиент: {{ $client->full_name }}</p>
<p>Авто: {{ $car->make }} {{ $car->model }} ({{ $car->license_plate }})</p>
<p>Срок аренды: с {{ $request->start_time }} по {{ $request->end_time }}</p>
</body>
</html>
