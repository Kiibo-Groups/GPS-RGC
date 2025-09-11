<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, 
    MirrorBeads, 
    vehicle_units, 
    TruckBoxes,
    GpsDevices
};

use Auth;
use DB;
use Validator;
use Redirect;
use IMS;
class TruckBoxesController extends Controller
{
    
	public $folder  = "admin/truckboxes.";
    
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

        // return response()->json([
        //     'data' 	=> TruckBoxes::with('GpsDevice')->OrderBy('created_at','DESC')->get(),
		// 	'link' 	=> '/truck_boxes/',
        //     'gps' => $chkAssign,
        //     'Models' => new TruckBoxes,
        //     'form_url_gps'	=> '/truck_boxes/assign_gps',
        // ]);

        return View($this->folder.'index',[
			'data' 	=> TruckBoxes::with('GpsDevice')->OrderBy('created_at','DESC')->get(),
			'link' 	=> '/truck_boxes/',
            'gps' => $chkAssign,
            'Models' => new TruckBoxes,
            'form_url_gps'	=> '/truck_boxes/assign_gps',
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
			'data' 		=> new TruckBoxes,
			'form_url' 	=> '/truck_boxes',
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
            $lims_truckboxes_data = new TruckBoxes;
            $lims_truckboxes_data->create($input);
            if($request->ajax() || $request->wantsJson())
            {
               return response()->json([
                    'status' => 'success',
                    'message' => 'Nueva Caja Agregada...',
                    'reload' => true
                ]);
            }

            return redirect(env('admin').'/truck_boxes')->with('message', 'Nueva Caja Agregada...');
        } catch (\Exception $th) {
            if(request()->ajax())
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error: '.$th->getMessage()
                ]);
            }

            return redirect(env('admin').'/truck_boxes')->with('error', $th->getMessage());
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
			'data' 		=> TruckBoxes::find($id),
			'form_url' 	=> '/truck_boxes/'.$id
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
            $lims_truckboxes_data = TruckBoxes::find($id);
            $lims_truckboxes_data->update($input);

            return redirect(env('admin').'/truck_boxes')->with('message', 'Caja Actualizada...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/truck_boxes')->with('error', $th->getMessage());
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
        // Validamos si la caja no esta asignada
        $chkAssign = vehicle_units::where('truck_boxes_id',$id)->get();
        if(count($chkAssign) > 0)
        {
            return redirect(env('admin').'/truck_boxes')->with('error','La caja esta asignada actualmente y no puede ser eliminada.');
        }else {
            TruckBoxes::where('id',$id)->delete();
            return redirect(env('admin').'/truck_boxes')->with('message','Caja eliminada con éxito...');
        }
    }

    /**
     * Cambio de status de la subcuenta.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status($id)
    {
        $res 			= TruckBoxes::find($id);
		$res->status 	= $res->status == 0 ? 1 : 0;
		$res->save();

		return redirect(env('admin').'/truck_boxes')->with('message','Estatus de la Caja actualizado con éxito...');
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
            $chkVehicle = TruckBoxes::find($data['truck_box_id']);
            $chkVehicle->gps_devices_id = $data['gps_devices_id'];
            $chkVehicle->save();
            return redirect(env('admin').'/truck_boxes')->with('message', 'GPS Asignado con éxito...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/truck_boxes')->with('error', $th->getMessage());
        }
    }

    public function delAssign($id)
    {
        try {
            $chkVehicle = TruckBoxes::find($id);
            $chkVehicle->gps_devices_id = null;
            $chkVehicle->save();
            return redirect(env('admin').'/truck_boxes')->with('message', 'Asignación eliminada con éxito...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/truck_boxes')->with('error', $th->getMessage());
        }
    }
}
