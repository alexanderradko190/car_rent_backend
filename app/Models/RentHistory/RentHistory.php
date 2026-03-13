<?php

namespace App\Models\RentHistory;

use App\Models\Car\Car;
use App\Models\Client\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_request_id',
        'car_id',
        'client_id',
        'start_time',
        'end_time',
        'total_cost'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
