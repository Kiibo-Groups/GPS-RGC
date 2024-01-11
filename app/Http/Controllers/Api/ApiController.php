<?php namespace App\Http\Controllers\api;

use App\Http\Requests;
use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use Redirect;
use JWTAuth;

class ApiController extends Controller 
{


    public function __construct()
	{
		$this->middleware('delivery:api');
	}

    public function welcome()
	{
		return response()->json(['data' => "Bienvenido al API de RGC"]);
	}

    public function Webhook_rgc_api(Request $request)
    {
        try {
			$validator = Validator::make($request->all(), [
				'unidad'        => 'required|string|min:15',
				'destino'       => 'required|string|min:100',
				'origen'        => 'required|string|min:100',
				'operador'      => 'required|string|min:50',
				'remitente'     => 'required|string|min:50',
				'destinatario'  => 'required|string|min:50',
				'remision'      => 'required|string|min:50',
				'caja'          => 'required|string|min:50',
			]);

			if ($validator->fails()) {
				$errors = $validator->errors();
				return response()->json(['data' => [], 'msg' => $errors], 400);
			}

            $data = $request->all();
			
			return response()->json($data, 200);
		} catch (\Exception $th) {
			return response()->json(['data' => [], 'msg' => $th->getMessage()], 500);
		}  
    }

}