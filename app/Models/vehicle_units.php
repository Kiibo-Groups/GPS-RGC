<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vehicle_units extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_unit',
        'id_unit',
        'registration_unit',
        'descript',
        'truck_boxes_id',
        'gps_devices_id',
        'status',
    ];

    public function getAssign()
    {
        return $this->hasOne('App\Models\Assignments');
    }
 
    public function getGPS()
    {
        return $this->belongsTo('App\Models\GpsDevices', 'gps_devices_id');
    }

    public function getBox()
    {
        return $this->belongsTo('App\Models\TruckBoxes', 'truck_boxes_id');
    }

    public function getBoxGPS()
    {
        return $this->getBox ? $this->getBox->GpsDevice : null;
    }

    public function GetNameGPS($id)
    {
        $req = GpsDevices::find($id);

        if (isset($req->id)) {
            return $req->uuid_device;
        }else {
            return 'Sin Asignar';
        }
    }

}
