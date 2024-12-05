<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin',
        'ApiKey_google',
        'stripe_api_id',
        'stripe_client_id',
        'SamsaraToken'
    ];

}
