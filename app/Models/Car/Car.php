<?php

namespace App\Models\Car;

use App\Enums\Car\CarStatus;
use App\Enums\Car\CarClass;
use App\Models\Client\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'make',
        'model',
        'year',
        'vin',
        'license_plate',
        'car_class',
        'power',
        'hourly_rate',
        'status',
        'current_renter_id'
    ];

    protected $casts = [
        'car_class' => CarClass::class,
        'status' => CarStatus::class,
        'hourly_rate' => 'float'
    ];

    public function renter()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
