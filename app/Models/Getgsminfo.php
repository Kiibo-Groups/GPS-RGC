<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Getgsminfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'length',
        'crc',
        'crc_status',
        'imei',
        'gps_devices_id',
        'vehicle_units_id',
        'command_id',
        'timestamp',
        'priority',
        'longitude',
        'latitude',
        'altitude',
        'angle',
        'satellites',
        'speed',
        'hdop',
        'event_io',
        'status',
        'status_code',
    ];

    public function getGPS()
    {
        return $this->belongsTo('App\Models\GpsDevices', 'gps_devices_id');
    }

}
