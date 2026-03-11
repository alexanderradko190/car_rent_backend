<?php

namespace App\Events\RentalRequest;

use App\Models\Car\Car;
use App\Models\Client\Client;
use App\Models\RentHistory\RentHistory;
use App\Models\RentalRequest\RentalRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RentalRequestIsCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly RentalRequest $rentalRequest,
        public readonly RentHistory $rentHistory,
        public readonly Car $car,
        public readonly Client $client,
    ) {
        //
    }
}
