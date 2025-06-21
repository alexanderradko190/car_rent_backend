<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'client_id',
        'start_time',
        'end_time',
        'total_cost',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
