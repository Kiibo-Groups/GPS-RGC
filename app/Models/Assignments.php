<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignments extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_units_id',
        'truck_box_id',
        'gps_devices_id',
    ];
 
    public function VehicleUnits()
    {
        return $this->belongsTo('App\Models\vehicle_units', 'vehicle_units_id' , 'id');
    }
    
}
