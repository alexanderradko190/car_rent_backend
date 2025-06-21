<?php

namespace Tests\Unit\RentalRequest;

use App\Models\Car;
use App\Services\RentalCostCalculator;
use PHPUnit\Framework\TestCase;

class RentalCostCalculatorTest extends TestCase
{
    private RentalCostCalculator $calc;
    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calc = new RentalCostCalculator();
        $this->car = new Car(['hourly_rate' => 1000]);
    }

    public function test_it_calculates_cost_without_insurance()
    {
        $cost = $this->calc->calculate($this->car, '2025-07-01 10:00:00', '2025-07-01 15:00:00', false);
        $this->assertEquals(5000, $cost);
    }

    public function test_it_calculates_cost_with_insurance()
    {
        $cost = $this->calc->calculate($this->car, '2025-07-01 10:00:00', '2025-07-01 15:00:00', true);
        $this->assertEquals(5500, $cost);
    }

    public function test_it_never_returns_less_than_one_hour_cost()
    {
        $cost = $this->calc->calculate($this->car, '2025-07-01 10:00:00', '2025-07-01 10:10:00', false);
        $this->assertEquals(1000, $cost);
    }

    public function test_it_works_for_multi_day_rent()
    {
        $cost = $this->calc->calculate($this->car, '2025-07-01 10:00:00', '2025-07-03 10:00:00', false);
        $this->assertEquals(48000, $cost);
    }
}
