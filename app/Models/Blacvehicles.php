<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blacvehicles extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'imei',
        'latitude',
        'longitude',
        'altitude',
        'engineStates',
        'speed',
        'azimuth',
        'odometer',
    ];
}
