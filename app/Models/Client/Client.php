<?php

namespace App\Models\Client;

use App\Models\Car\Car;
use App\Models\RentHistory\RentHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'age',
        'phone',
        'email',
        'driving_experience',
        'license_scan',
    ];

    public function rentHistories(): HasMany
    {
        return $this->hasMany(RentHistory::class, 'client_id');
    }

    public function car(): HasOne
    {
        return $this->hasOne(Car::class, 'current_renter_id');
    }
}
