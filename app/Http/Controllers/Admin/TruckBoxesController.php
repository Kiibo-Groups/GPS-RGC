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
        return View($this->folder.'index',[
			'data' 	=> TruckBoxes::get(),
			'link' 	=> '/truck_boxes/',
            'gps' => GpsDevices::where('status',0)->get(),
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

            return redirect(env('admin').'/truck_boxes')->with('message', 'Nueva Caja Agregada...');
        } catch (\Exception $th) {
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
        TruckBoxes::where('id',$id)->delete();
		return redirect(env('admin').'/truck_boxes')->with('message','Caja eliminada con éxito...');
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
            $data = $request->all();
            $chkVehicle = TruckBoxes::find($data['truck_box_id']);
            $chkVehicle->gps = $data['gps_devices_id'];
            $chkVehicle->save();
            return redirect(env('admin').'/truck_boxes')->with('message', 'GPS Asignado con éxito...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/truck_boxes')->with('error', $th->getMessage());
        }
    }
}
