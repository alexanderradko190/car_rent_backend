<?php

namespace App\Http\Controllers;

use App\Enums\CarClass;

class RentalRequestController extends Controller
{
    private function getClassRate(CarClass $class): float
    {
        return $class->hourlyRate();
    }
}
