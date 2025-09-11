<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commands extends Model
{
    use HasFactory;

    protected $fillable = [
        'gps_devices_id',
        'name_command',
        'password_command',
        'command',
        'status',
    ];
    
}
