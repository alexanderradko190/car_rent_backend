<?php

namespace App\Services;

use App\Models\Car;
use DateTime;

class RentalCostCalculator
{
    public function calculate(Car $car, string $start, string $end, bool $insurance = false): float
    {
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);

        $interval = $startDate->diff($endDate);
        $hours = ($interval->days * 24) + $interval->h;

        if ($hours < 1) {
            $hours = 1;
        }

        $cost = $car->hourly_rate * $hours;

        if ($insurance) {
            $cost += 100 * $hours;
        }

        return $cost;
    }
}

