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
        // $data = vehicle_units::get();
        // return response()->json([
        //     'data' 	=>  $data
        // ]);

        return View($this->folder.'index',[
			'data' 	=> vehicle_units::get(),
			'link' 	=> '/vehicle_units/',
            'boxes' => TruckBoxes::where('status',0)->get(),
            'gps' => GpsDevices::where('status',0)->get(),
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

            return redirect(env('admin').'/vehicle_units')->with('message', 'Nueva Unidad Agregada...');
        } catch (\Exception $th) {
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
            $data = $request->all();
            
            // Validamos la existencia
            $chkVehicle = vehicle_units::find($data['vehicle_units_id']);
            $chkVehicle->box = $data['truck_box_id'];
            $chkVehicle->save();

            return redirect(env('admin').'/vehicle_units')->with('message', 'Unidad Asignada con éxito...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/vehicle_units')->with('error', $th->getMessage());
        }
    }

    public function assign_gps(Request $request)
    { 

        try {
            $data = $request->all();
            $chkVehicle = vehicle_units::find($data['vehicle_units_id']);
            $chkVehicle->gps = $data['gps_devices_id'];
            $chkVehicle->save();
            return redirect(env('admin').'/vehicle_units')->with('message', 'GPS Asignado con éxito...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/vehicle_units')->with('error', $th->getMessage());
        }
    }
}
