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
        // return response()->json([
        //     'data' 	=> ChatsInbox::get(),
		// 	'form_url' 	=> '/chats_inbox/'
        // ]);

        return View($this->folder.'index',[
			'data' 	        => ChatsInbox::where('cc',0)->OrderBy('id','DESC')->get(),
            'bandeja'       => ChatsInbox::where('ready','0')->count(),  // 0 = no leido, 1 = Leido
            // 0 = Enviado Admin, 1 = Enviado por usuario, 2 = eliminado

            'sends'         => (Auth::user()->role == 1) ? ChatsInbox::where('status','0')->count() : ChatsInbox::where('status','1')->count(), 
            'dels'          => ChatsInbox::where('status','2')->count(), // 0 = Enviado Admin, 1 = Enviado por usuario, 2 = eliminado
            'list_users'    => User::where('role','!=','1')->get(),
			'form_url' 	    => '/chats_inbox/'
		]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function view_inbox($id)
    {
        $inboxs = new ChatsInbox;
        $inbox = ChatsInbox::find($id);
        // Lo marcamos como leido
        if ($inbox->ready == 0) {
            
            $inbox->ready = 1;
            $inbox->save();
        }

        // return response()->json([
        //     'data' 	        => $inboxs->getInbox($id)
        // ]);

        return View($this->folder.'view_msg',[
			'data' 	        => $inboxs->getInbox($id),
            'bandeja'       => ChatsInbox::where('ready','0')->count(),  // 0 = no leido, 1 = Leido
            // 0 = Enviado Admin, 1 = Enviado por usuario, 2 = eliminado
            'sends'         => (Auth::user()->role == 1) ? ChatsInbox::where('status','0')->count() : ChatsInbox::where('status','1')->count(), 
            'dels'          => ChatsInbox::where('status','2')->count(), // 0 = Enviado Admin, 1 = Enviado por usuario, 2 = eliminado
            'list_users'    => User::where('role','!=','1')->get(),
			'form_url' 	    => '/chats_inbox/',
            'form_reply'    => '/chats_reply_inbox/'
		]);
    }

    public function chats_reply_inbox(Request $request)
    {
        try {
            $input = $request->all();
            $lims_inbox_data = new ChatsInbox;
            
            $input['user_id']   = $input['user_id'];
            $input['cc']        = $input['reply_cc'];
            $input['subject']   = $input['subject'];
            $input['message']   = $input['message'];
            $input['ready']     = 0; // 0 = no leido, 1 = Leido
            $input['status']    = (Auth::user()->role == 1) ? 0 : 1; // 0 = Enviado Admin, 1 = Enviado por usuario, 2 = eliminado
            
            $lims_inbox_data->create($input);

            return response()->json([
                'status' => true,
                'code'  => 200,
                'msg'   => $input
            ]);
            
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'code'  => 500,
                'msg'   => $th->getMessage()
            ]);
        }
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
            
            $input['user_id']   = $input['user_id'];
            
            $input['subject']   = $input['subject'];
            $input['message']   = $input['message'];
            $input['ready']     = 0; // 0 = no leido, 1 = Leido
            $input['status']    = (Auth::user()->role == 1) ? 0 : 1;; // 0 = Enviado Admin, 1 = Enviado por usuario, 2 = eliminado
            
            $lims_inbox_data->create($input);

            return response()->json([
                'status' => true,
                'code'  => 200,
                'msg'   => 'msg_send'
            ]);
            
        } catch (\Exception $th) {
            return response()->json([
                'status' => false,
                'code'  => 500,
                'msg'   => $th->getMessage()
            ]);
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
