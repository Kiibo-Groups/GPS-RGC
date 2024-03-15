<?php namespace App\Http\Controllers\api;

use App\Http\Requests;
use App\Http\Controllers\Controller; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Contracts\JWTSubject; 
use DB;
use Validator;
use Redirect; 
use App\Models\{User};

class ApiController extends Controller 
{
    public function __construct()
	{
		$this->middleware('authApi:api',['except' => ['getToken']]);
	}

    public function welcome()
	{
		return response()->json(['data' => "Bienvenido al API de RGC"]);
	}

	public function getToken(Request $request)
    {
        try {
            $token = new User;
            return response()->json($token->GenToken($request));
        } catch (\Exception $th) {
			return response()->json(['status' => 'ERROR','code' => 500, 'message' => $th->getMessage()], 500);
		}
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
				return response()->json([
					'data' => [],
					'message' => $errors,
					'status' => "FAILE",
					"code" => 400
				]);
			}

            $data = $request->all();
			
			return response()->json($data, 200);
		} catch (\Exception $th) {
			return response()->json([
				'data' => [],
				'message' => $th->getMessage(),
				'status' => "FAILE",
				"code" => 500
			]);
		}  
    }
}