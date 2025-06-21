<?php

namespace App\Models;

use App\Enums\RentalStatus;
use Database\Factories\RentalRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public static function factory(...$parameters)
    {
        return RentalRequestFactory::new();
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
