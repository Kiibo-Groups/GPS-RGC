<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{User, MirrorBeads};

use Auth;
use DB;
use Validator;
use Redirect;
use IMS;

class MirrorBeadsController extends Controller
{
    
	public $folder  = "admin/mirrorbeads.";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View($this->folder.'index',[
			'data' 	=> User::where('role',2)->get(),
			'link' 	=> '/mirror_beads/'
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
			'data' 		=> new User,
			'form_url' 	=> '/mirror_beads',
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
            $lims_mirrorbeads_data = new User;

            

            $input['role']           = 2; // 0 = Admin, 1 = SubAccount, 2 Cuenta Espejo
            $input['name']           = ucwords($input['name']);
            $input['password']       =  bcrypt($input['password']);
            $input['show_password']  =  $input['password'];
 
            $lims_mirrorbeads_data->create($input);

            return redirect(env('admin').'/mirror_beads')->with('message', 'Nueva Cuenta Agregada...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/mirror_beads/create')->with('error', $th->getMessage());
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
			'data' 		=> User::find($id),
			'form_url' 	=> '/mirror_beads/'.$id
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
            $lims_mirrorbeads_data = User::find($id);

            if (isset($input['new_password'])) {
                $input['password']       =  bcrypt($input['new_password']);
                $input['show_password']   =  $input['new_password'];
            }

            $lims_mirrorbeads_data->update($input);

            return redirect(env('admin').'/mirror_beads')->with('message', 'Cuenta Actualizada...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/mirror_beads')->with('error', $th->getMessage());
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
        User::where('id',$id)->delete();
		return redirect(env('admin').'/mirror_beads')->with('message','Cuenta eliminada con éxito...');
    }

    /**
     * Cambio de status de la subcuenta.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status($id)
    {
        $res 			= User::find($id);
		$res->status 	= $res->status == 0 ? 1 : 0;
		$res->save();

		return redirect(env('admin').'/mirror_beads')->with('message','Estatus actualizado con éxito...');
    }
}