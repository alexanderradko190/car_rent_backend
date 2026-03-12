<?php

namespace App\Models\RentalRequest;

use App\Models\Car\Car;
use App\Models\Client\Client;
use App\Models\RentHistory\RentHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgreementDelivery extends Model
{
    protected $fillable = [
        'rental_request_id',
        'rent_history_id',
        'car_id',
        'client_id',
        'status',
        'agreement_path',
        'error',
        'generated_at',
        'sent_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function rentalRequest(): BelongsTo
    {
        return $this->belongsTo(RentalRequest::class, 'rental_request_id');
    }

    public function rentHistory(): BelongsTo
    {
        return $this->belongsTo(RentHistory::class, 'rent_history_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
