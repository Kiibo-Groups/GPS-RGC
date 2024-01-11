<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GpsDevices extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_device',
        'uuid_device',
        'descript_device',
        'status',
    ];
}
