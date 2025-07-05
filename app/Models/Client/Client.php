<?php

namespace App\Models\Client;

use App\Models\RentHistory\RentHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'age',
        'phone',
        'email',
        'driving_experience',
        'license_scan',
    ];

    public function rentHistories()
    {
        return $this->hasMany(RentHistory::class, 'client_id');
    }
}
