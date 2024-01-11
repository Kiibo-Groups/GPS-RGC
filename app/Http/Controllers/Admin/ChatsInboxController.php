<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, 
    MirrorBeads, 
    vehicle_units, 
    TruckBoxes,
    GpsDevices,
    ChatsInbox
};

use Auth;
use DB;
use Validator;
use Redirect;
use IMS;
class ChatsInboxController extends Controller
{
	public $folder  = "admin/inbox.";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View($this->folder.'index',[
			'data' 	=> ChatsInbox::get(),
			'link' 	=> '/chats_inbox/'
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
			'data' 		=> new ChatsInbox,
			'form_url' 	=> '/chats_inbox',
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
            $lims_inbox_data = new ChatsInbox;
            $lims_inbox_data->create($input);

            return redirect(env('admin').'/chats_inbox')->with('message', 'Nuevo Mensaje enviado...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/chats_inbox')->with('error', $th->getMessage());
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
			'data' 		=> ChatsInbox::find($id),
			'form_url' 	=> '/chats_inbox/'.$id
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
            $lims_inbox_data = ChatsInbox::find($id);
            $lims_inbox_data->update($input);

            return redirect(env('admin').'/chats_inbox')->with('message', 'Mensaje Actualizado...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/chats_inbox')->with('error', $th->getMessage());
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
        ChatsInbox::where('id',$id)->delete();
		return redirect(env('admin').'/chats_inbox')->with('message','Mensaje eliminado con éxito...');
    }

    /**
     * Cambio de status de la subcuenta.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status($id)
    {
        $res 			= ChatsInbox::find($id);
		$res->status 	= $res->status == 0 ? 1 : 0;
		$res->save();

		return redirect(env('admin').'/chats_inbox')->with('message','Estatus del Mensaje actualizado con éxito...');
    }
}
