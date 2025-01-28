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
        'box',
        'gps',
        'status',
    ];

    public function getAssign()
    {
        return $this->hasOne('App\Models\Assignments');
    }
 

    public function GetNameTruckBox($id)
    {
        $req = TruckBoxes::find($id);

        if (isset($req->id)) {
            return $req->name_truck_box;
        }else {
            return 'Sin Asignar';
        }
    }

    public function GetNameGPS($id)
    {
        $req = GpsDevices::find($id);

        if (isset($req->id)) {
            return $req->name_device;
        }else {
            return 'Sin Asignar';
        }
    }

}
