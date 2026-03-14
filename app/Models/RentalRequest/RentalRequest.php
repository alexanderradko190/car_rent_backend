<?php

namespace App\Models\RentalRequest;

use App\Enums\Car\RentalStatus;
use App\Models\Car\Car;
use App\Models\Client\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'car_id',
        'start_time',
        'end_time',
        'total_cost',
        'insurance_option',
        'status',
        'agreement_path'
    ];

    protected $casts = [
        'status' => RentalStatus::class,
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'insurance_option' => 'boolean'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
