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
class GpsDevicesController extends Controller
{
    
	public $folder  = "admin/devices.";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View($this->folder.'index',[
			'data' 	=> GpsDevices::get(),
			'link' 	=> '/dispositivos/'
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
			'data' 		=> new GpsDevices,
			'form_url' 	=> '/dispositivos',
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
            $lims_devices_data = new GpsDevices;
            $lims_devices_data->create($input);

            return redirect(env('admin').'/dispositivos')->with('message', 'Nuev Dispositivo Agregado...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/dispositivos')->with('error', $th->getMessage());
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
			'data' 		=> GpsDevices::find($id),
			'form_url' 	=> '/dispositivos/'.$id
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
            $lims_devices_data = GpsDevices::find($id);
            $lims_devices_data->update($input);

            return redirect(env('admin').'/dispositivos')->with('message', 'Dispositivo Actualizado...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/dispositivos')->with('error', $th->getMessage());
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
        GpsDevices::where('id',$id)->delete();
		return redirect(env('admin').'/dispositivos')->with('message','Dispositivo eliminado con éxito...');
    }

    /**
     * Cambio de status de la subcuenta.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status($id)
    {
        $res 			= GpsDevices::find($id);
		$res->status 	= $res->status == 0 ? 1 : 0;
		$res->save();

		return redirect(env('admin').'/dispositivos')->with('message','Estatus del Dispositivo actualizado con éxito...');
    }
}
