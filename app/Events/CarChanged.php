<?php

namespace App\Events;

use App\Models\Car\Car;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ?Car $car = null,
        public string $action = 'changed'
    ) {
        //
    }
}
