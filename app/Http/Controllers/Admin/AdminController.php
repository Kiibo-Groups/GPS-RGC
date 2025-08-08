<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\{BlacsolController, SamsaraController};
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use DB;
use Validator;

use App\Models\{
	User, 
	Settings, 
	Getgsminfo
};

class AdminController extends Controller
{
	public $folder = "admin.";

    /*
	|------------------------------------------------------------------
	|Index page for login
	|------------------------------------------------------------------
	*/
	public function index()
	{ 
		return View($this->folder.'dashboard.home',[ 
            'ApiKey_google' => Settings::find(1)->ApiKey_google
		]);
	}

	/*
	|------------------------------------------------------------------
	|Homepage, Dashboard
	|------------------------------------------------------------------
	*/
	public function home()
	{
		return View($this->folder.'dashboard.home',[ 
            'ApiKey_google' => Settings::find(1)->ApiKey_google
		]);
	}

	/*
	|------------------------------------------------------------------
	|Account Settings
	|------------------------------------------------------------------
	*/
	public function account()
	{
		$data = User::find(User::find(Auth::user()->id))->first();  
    
        return view($this->folder.'dashboard.account', [ 
            'data' => $data,
            'form_url'	=> Asset('/update_account'),
        ]); 
	}

	public function update_account(Request $request)
	{
		try {
			$lim_data_account = User::find(Auth::user()->id);
			$input = $request->all();
			$switchPsw = false;
			// return response()->json([
			// 	'user' => $lim_data_account,
			// 	'data' => $input
			// ]);


			if (isset($input['logo']) && $input['logo'] != null) {
				$image = $request->logo;

				// Verificamos si ya tenia una imagen anterior
				if ($lim_data_account->logo != NULL) { 
					// Validamos que no sea la imagen por defecto
				    if ($lim_data_account->logo != 'user-1.png') {
						@unlink('assets/images/users/'.$lim_data_account->logo);
					}
				}
				
				$ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
				$imageName = date("Ymdhis");
				$imageName = $imageName . '.' . $ext;
				$image->move('assets/images/users', $imageName);
	
				$input['logo'] = $imageName;
			}

			if (isset($input['logo_top']) && $input['logo_top'] != null) {
				$imageTop = $request->logo_top;

				// Verificamos si ya tenia una imagen anterior
				if ($lim_data_account->logo_top != NULL) { 
					// Validamos que no sea la imagen por defecto
				    if ($lim_data_account->logo_top != 'logo-top.png') {
						@unlink('assets/images/users/'.$lim_data_account->logo_top);
					}
				}
				
				$ext = pathinfo($imageTop->getClientOriginalName(), PATHINFO_EXTENSION);
				$imageName = date("Ymdhis");
				$imageName = $imageName . '.' . $ext;
				$imageTop->move('assets/images/users', $imageName);
	
				$input['logo_top'] = $imageName;
			}

			if (isset($input['logo_top_sm']) && $input['logo_top_sm'] != null) {
				$imageTopSm = $request->logo_top_sm;

				// Verificamos si ya tenia una imagen anterior
				if ($lim_data_account->logo_top_sm != NULL) { 
					// Validamos que no sea la imagen por defecto
				    if ($lim_data_account->logo_top_sm != 'logo-sm.png') {
						@unlink('assets/images/users/'.$lim_data_account->logo_top_sm);
					}
				}
				
				$ext = pathinfo($imageTopSm->getClientOriginalName(), PATHINFO_EXTENSION);
				$imageName = date("Ymdhis");
				$imageName = $imageName . '.' . $ext;
				$imageTopSm->move('assets/images/users', $imageName);
	
				$input['logo_top_sm'] = $imageName;
			}

			if (isset($input['new_password']) && $input['new_password'] != null) {
				// Cambiamos la contraseña
				$input['shw_password'] = $input['new_password'];
                $input['password'] = bcrypt($input['new_password']);
				$switchPsw = true;
			}

			$lim_data_account->update($input);

			if (!$switchPsw) {
				return redirect('/account')->with('message', 'Datos de la cuenta actualizada con éxito ...');
			}else {
				auth()->guard()->logout();
				return Redirect::to('/')->with('message', 'Tu contraseña ha sido cambiada, por favor vuelve a iniciar sesión');
			}
        } catch (\Exception $th) {
            return redirect('account')->with('error', $th->getMessage());
        }
	}

	public function ajustes()
	{
		return view($this->folder.'dashboard.ajustes', [ 
            'data' => Settings::where('admin',Auth::user()->id)->first(),
            'form_url'	=> Asset('/update_ajustes'),
        ]); 
	}

	public function update_ajustes(Request $request)
	{
		try {
			$lim_data_settings = Settings::where('admin',Auth::user()->id)->first();
			$input['admin'] = Auth::user()->id;
			$input['ApiKey_google'] = $request->get('ApiKey_google');
			$input['stripe_api_id'] = $request->get('stripe_api_id');
			$input['stripe_client_id'] = $request->get('stripe_client_id');
			
			$lim_data_settings->update($input);

            return redirect('/ajustes')->with('message', 'Configuración actualizada con éxito ...');
        } catch (\Exception $th) {
            return redirect('ajustes')->with('error', $th->getMessage());
        }

	}


	public function conexiones()
	{
		return View($this->folder.'dashboard.home');
	}

	
	/*
	|------------------------------------------------------------------
	|Seguimiento de GPS
	|------------------------------------------------------------------
	*/
	public function trackings()
	{
		$getAll = Getgsminfo::where('gps_devices_id','!=', null)
			->with('getGPS', 'getVehicle')
			->get([
				'id',
				'longitude',
				'latitude',
				'altitude',
				'angle',
				'speed',
				'hdop',
				'event_io',
				'gps_devices_id',
				'vehicle_units_id',
				'date_update'
			]);

		$devices = collect($getAll)->sortByDesc('date_update')->values();
		$ApiKey_google = Settings::find(1)->ApiKey_google;


		return view($this->folder.'dashboard.trackings', compact('devices', 'ApiKey_google'));
	}

	/*
	|------------------------------------------------------------------
	|Logout
	|------------------------------------------------------------------
	*/
	public function logout()
	{
		auth()->guard()->logout();

		return Redirect::to('/')->with('message', 'Ha cerrado sesión con éxito !');
	}
}
