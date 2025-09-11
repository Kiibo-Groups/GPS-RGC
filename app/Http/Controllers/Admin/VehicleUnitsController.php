<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, 
    MirrorBeads, 
    vehicle_units, 
    TruckBoxes,
    GpsDevices,
    Assignments
};

use Auth;
use DB;
use Validator;
use Redirect;
use IMS;
class VehicleUnitsController extends Controller
{
    
	public $folder  = "admin/vehiclesUnit.";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {         
        // Obtenemos todos los GPS que no esten asignados
        $chkAssign = GpsDevices::where('status',0)->get();
        foreach ($chkAssign as $key => $value) {
            $chkBox = TruckBoxes::where('gps_devices_id',$value->id)->first();
            $chkVehicle = vehicle_units::where('gps_devices_id',$value->id)->first();
            if($chkBox || $chkVehicle)
            {
                unset($chkAssign[$key]);
            }
        }

        // Obtenemos todas las cajas que no esten asignadas
        $chkBoxAssign = TruckBoxes::where('status',0)->get();
        foreach ($chkBoxAssign as $key => $value) {
            $chkVehicle = vehicle_units::where('truck_boxes_id',$value->id)->first();
            if($chkVehicle)
            {
                unset($chkBoxAssign[$key]);
            }
        }

        return View($this->folder.'index',[
			'data' 	=> vehicle_units::with(['getGPS', 'getBox.GpsDevice'])->get(),
			'link' 	=> '/vehicle_units/',
            'boxes' => $chkBoxAssign,
            'gps' => $chkAssign,
            'Models' => new vehicle_units,
            'form_url_box'	=> '/vehicle_units/assign_box',
            'form_url_gps'	=> '/vehicle_units/assign_gps',
		]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return View($this->folder.'add',[
			'data' 		=> new vehicle_units,
			'form_url' 	=> '/vehicle_units',
            'array'		=> []
		]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $lims_vehicles_data = new vehicle_units;
            $lims_vehicles_data->create($input);
            
            if($request->ajax() || $request->wantsJson())
            {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Nueva Unidad Agregada...',
                    'reload' => true
                ]);
            }

            return redirect(env('admin').'/vehicle_units')->with('message', 'Nueva Unidad Agregada...');
        } catch (\Exception $th) {
            if(request()->ajax())
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error: '.$th->getMessage()
                ]);
            }
            
            return redirect(env('admin').'/vehicle_units')->with('error', $th->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
        return View($this->folder.'edit',[
			'data' 		=> vehicle_units::find($id),
			'form_url' 	=> '/vehicle_units/'.$id
		]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $input = $request->all();
            $lims_vehicles_data = vehicle_units::find($id);
            $lims_vehicles_data->update($input);

            return redirect(env('admin').'/vehicle_units')->with('message', 'Unidad Actualizada...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/vehicle_units')->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        vehicle_units::where('id',$id)->delete();
		return redirect(env('admin').'/vehicle_units')->with('message','Unidad eliminada con éxito...');
    }

    /**
     * Cambio de status de la subcuenta.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status($id)
    {
        $res 			= vehicle_units::find($id);
		$res->status 	= $res->status == 0 ? 1 : 0;
		$res->save();

		return redirect(env('admin').'/vehicle_units')->with('message','Estatus de la Unidad actualizado con éxito...');
    }

    public function assign_box(Request $request)
    {
        // return response()->json([
        //     'data' => $request->all()
        // ]);

        try {
            // Verificamos que el GPS no este asignado a otro vehiculo
            $chkAssign = TruckBoxes::where('gps_devices_id',$request->gps_devices_id)->first();
            if($chkAssign)
            {
                return redirect(env('admin').'/truck_boxes')->with('error','El dispositivo GPS ya esta asignado a otra caja.');
            }

            // Verificamos que el GPS no este asignado a otro vehiculo
            $chkAssignVehicle = vehicle_units::where('gps_devices_id',$request->gps_devices_id)->first();
            if($chkAssignVehicle)
            {
                return redirect(env('admin').'/truck_boxes')->with('error','El dispositivo GPS ya esta asignado a un vehículo.');
            }

            $data = $request->all();
            
            // Validamos la existencia
            $chkVehicle = vehicle_units::find($data['vehicle_units_id']);
            $chkVehicle->truck_boxes_id = $data['truck_box_id'];
            $chkVehicle->save();

            return redirect(env('admin').'/vehicle_units')->with('message', 'Unidad Asignada con éxito...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/vehicle_units')->with('error', $th->getMessage());
        }
    }

    public function assign_gps(Request $request)
    { 
        try {
            // Verificamos que el GPS no este asignado a otro vehiculo
            $chkAssign = TruckBoxes::where('gps_devices_id',$request->gps_devices_id)->first();
            if($chkAssign)
            {
                return redirect(env('admin').'/truck_boxes')->with('error','El dispositivo GPS ya esta asignado a otra caja.');
            }

            // Verificamos que el GPS no este asignado a otro vehiculo
            $chkAssignVehicle = vehicle_units::where('gps_devices_id',$request->gps_devices_id)->first();
            if($chkAssignVehicle)
            {
                return redirect(env('admin').'/truck_boxes')->with('error','El dispositivo GPS ya esta asignado a un vehículo.');
            }

            $data = $request->all();
            $chkVehicle = vehicle_units::find($data['vehicle_units_id']);
            $chkVehicle->gps_devices_id = $data['gps_devices_id'];
            $chkVehicle->save();
            return redirect(env('admin').'/vehicle_units')->with('message', 'GPS Asignado con éxito...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/vehicle_units')->with('error', $th->getMessage());
        }
    }

    public function delAssign($id, $type)
    {
        try {
            
            if($type == 'caja')
            {
                $chkVehicle = vehicle_units::find($id);
                $chkVehicle->truck_boxes_id = null;
                $chkVehicle->save();
                return redirect(env('admin').'/vehicle_units')->with('message', 'Asignación eliminada con éxito...');
            }
            
            // Eliminamos la asignacion del GPS
            $chkVehicle = vehicle_units::find($id);
            $chkVehicle->gps_devices_id = null;
            $chkVehicle->save();
            return redirect(env('admin').'/vehicle_units')->with('message', 'Asignación eliminada con éxito...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/vehicle_units')->with('error', $th->getMessage());
        }
    }
}
