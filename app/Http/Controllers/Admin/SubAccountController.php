<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Auth;
use DB;
use Validator;
use Redirect;
use IMS;
class SubAccountController extends Controller
{
    
	public $folder  = "admin/subAccount.";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return View($this->folder.'index',[
			'data' 	=> User::where('id','!=',1)->where('role',1)->get(),
			'link' 	=> '/subaccounts/'
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
			'form_url' 	=> '/subaccounts',
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
        // return response()->json(['data' => $request->all()]);

        try {
            $input = $request->all();
            $lims_settings_data = new User;
            
            $data['role']           = 1;
            $data['name']           = ucwords($input['name']);
            $data['username']       = $input['username'];
            $data['email']          = $input['email'];

            $data['password']       =  bcrypt($input['password']);
            $data['shw_password']   =  $input['password'];

            $data['perm']           = implode(",", $input['perm']);
           
            $data['status']         = $input['status'];
            
            $lims_settings_data->create($data);
            return redirect(env('admin').'/subaccounts')->with('message', 'Nueva Subcuenta agregada...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/subaccounts')->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin = User::find($id);

        return View($this->folder.'edit',[
			'data' 		=> User::find($id),
			'form_url' 	=> '/subaccounts/'.$id,
            'array'		=> explode(",", $admin->perm)
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
            $lims_settings_data = User::find($id);
            
            $data['name']           = ucwords($input['name']);
            $data['username']       = $input['username'];
            $data['email']          = $input['email'];

            if (isset($input['new_password'])) {
                $data['password']       =  bcrypt($input['new_password']);
                $data['shw_password']   =  $input['new_password'];
            }

            $data['perm']           = implode(",", $input['perm']);
           
            $data['status']         = $input['status']; 
            
            $lims_settings_data->update($data);
            return redirect(env('admin').'/subaccounts')->with('message', 'Subcuenta actualizada con éxito...');
        } catch (\Exception $th) {
            return redirect(env('admin').'/subaccounts')->with('error', $th->getMessage());
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
		return redirect(env('admin').'/subaccounts')->with('message','SubCuenta eliminada con éxito...');
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

		return redirect(env('admin').'/subaccounts')->with('message','Estatus actualizado con éxito...');
    }
}
