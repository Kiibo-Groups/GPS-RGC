<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rutas;
use App\Models\Settings;

use Auth;
use DB;
use Validator;
use Redirect;
use IMS;
class RutasController extends Controller
{
	public $folder  = "admin/Rutas.";
    /**
     * Display a listing of the resource.
     * 
     */
    public function index()
    {
        return View($this->folder.'index',[
			'data' 	=> Rutas::OrderBy('id','DESC')->get(),
            'ApiKey_google' => Settings::find(1)->ApiKey_google,
			'link' 	=> '/rutas/'
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
			'data' 		=> new Rutas,
			'form_url' 	=> '/subaccounts',
            'array'		=> []
		]);
    }
    public function getPoly($origin, $destin)
	{ 
		$url = "https://maps.googleapis.com/maps/api/directions/json?origin=".$origin."&destination=".$destin."&mode=driving&key=".Settings::find(1)->ApiKey_google;
		$max      = 0;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec ($ch);
        $info = curl_getinfo($ch);
        $http_result = $info['http_code'];
        curl_close ($ch);


		$req_routes = json_decode($output, true); 


		return response()->json([
            'data' => $req_routes,
            'url'  => $url
        ]);
	}
}
