<?php

namespace App\Helpers;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArrayReportExport implements FromArray, WithHeadings
{
    public function __construct(
        private readonly string $type,
        private readonly array $rows
    ) {}

    public function headings(): array
    {
        return match ($this->type) {
            'rent_histories' => [
                'ID',
                'VIN',
                'Гос. номер',
                'ФИО клиента',
                'Телефон клиента',
                'Начало аренды',
                'Завершение аренды',
                'Сумма',
                'Длительность (мин)',
            ],
            'rental_requests' => [
                'ID',
                'VIN',
                'Гос. номер',
                'ФИО клиента',
                'Телефон клиента',
                'Начало аренды',
                'Завершение аренды',
                'Статус',
                'Создана',
                'Длительность (мин)',
            ],
            default => [],
        };
    }

    public function array(): array
    {
        return match ($this->type) {
            'rent_histories' => array_map(function (array $row) {
                $start = !empty($row['start_time']) ? Carbon::parse($row['start_time']) : null;
                $end   = !empty($row['end_time']) ? Carbon::parse($row['end_time']) : null;

                $duration = ($start && $end && $end->greaterThanOrEqualTo($start))
                    ? $start->diffInMinutes($end)
                    : '';

                return [
                    $row['id'] ?? '',
                    $row['vin'] ?? '',
                    $row['license_plate'] ?? '',
                    $row['full_name'] ?? '',
                    $row['phone'] ?? '',
                    $start?->format('Y-m-d H:i:s') ?? '',
                    $end?->format('Y-m-d H:i:s') ?? '',
                    $row['total_cost'] ?? '',
                    $duration,
                ];
            }, $this->rows),

            'rental_requests' => array_map(function (array $row) {
                $start   = !empty($row['start_time']) ? Carbon::parse($row['start_time']) : null;
                $end     = !empty($row['end_time']) ? Carbon::parse($row['end_time']) : null;
                $created = !empty($row['created_at']) ? Carbon::parse($row['created_at']) : null;

                $duration = ($start && $end && $end->greaterThanOrEqualTo($start))
                    ? $start->diffInMinutes($end)
                    : '';

                return [
                    $row['id'] ?? '',
                    $row['vin'] ?? '',
                    $row['license_plate'] ?? '',
                    $row['full_name'] ?? '',
                    $row['phone'] ?? '',
                    $start?->format('Y-m-d H:i:s') ?? '',
                    $end?->format('Y-m-d H:i:s') ?? '',
                    $row['status'] ?? '',
                    $created?->format('Y-m-d H:i:s') ?? '',
                    $duration,
                ];
            }, $this->rows),

            default => [],
        };
    }
}
