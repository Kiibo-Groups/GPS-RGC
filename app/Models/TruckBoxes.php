<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TruckBoxes extends Model
{
    use HasFactory;
    protected $fillable = [
        'name_truck_box',
        'id_truck_box',
        'descript_truck_box',
        'status',
    ];

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
