<?php namespace App\Http\Controllers\api;

use App\Http\Requests;
use App\Http\Controllers\Controller; 
use App\Providers\SocketServer;
use App\Http\Controllers\{BlacsolController, SamsaraController};
use Illuminate\Support\Facades\Log;

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
		$this->middleware('authApi:api',['except' => [
			'getToken', 
			'getDevice',
			'PosiCont',
			'Idle',
			'Mov',
			'Detenido',
			'IgnEnc',
			'IgnApa',
			'DetJamm',
			'DesconBat',
			'ReconBat',]]);
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

	public function getDevice()
	{

		$server = new SocketServer("185.213.2.33",31337); // Create a Server binding to the given ip address and listen to port 31337 for connections
		$server->max_clients = 10; // Allow no more than 10 people to connect at a time
		$server->hook("CONNECT","handle_connect"); // Run handle_connect every time someone connects
		$server->hook("INPUT","handle_input"); // Run handle_input whenever text is sent to the server
		$server->infinite_loop(); // Run Server Code Until Process is terminated.


		function handle_connect(&$server,&$client,$input)
		{
			SocketServer::socket_write_smart($client->socket,"String? ","");
		}
		function handle_input(&$server,&$client,$input)
		{
			// You probably want to sanitize your inputs here
			$trim = trim($input); // Trim the input, Remove Line Endings and Extra Whitespace.

			if(strtolower($trim) == "quit") // User Wants to quit the server
			{
				SocketServer::socket_write_smart($client->socket,"Oh... Goodbye..."); // Give the user a sad goodbye message, meany!
				$server->disconnect($client->server_clients_index); // Disconnect this client.
				return; // Ends the function
			}

			$output = strrev($trim); // Reverse the String

			SocketServer::socket_write_smart($client->socket,$output); // Send the Client back the String
			SocketServer::socket_write_smart($client->socket,"String? ",""); // Request Another String
		}
	}

	/**
	 * Conexion con el WebService
	 * BlackSoul
	 */
	public function PosiCont()
	{
		Log::channel()->info('[*]['.date('H:i:s')."] Inicializacion de Peticion de PosiCont.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			$req  = [];
			// for ($i=0; $i < count($data)-1; $i++) { 
			// 	$BlacSol = new BlacsolController(
			// 		$data[$i]['username'],	// username
			// 		$data[$i]['imei'],	// imei
			// 		$data[$i]['latitude'],	// latitude
			// 		$data[$i]['longitude'],	// longitude
			// 		$data[$i]['altitude'],	// altitude
			// 		$data[$i]['speed'],	// speed
			// 		$data[$i]['azimuth'],	// azimuth
			// 		$data[$i]['odometer'],	// odometer
			// 		$data[$i]['dateTimeUTC']	// dateTimeUTC
			// 	);
				
			// 	$req[] = $BlacSol->PosiCont();
			// 	unset($BlacSol);
			// } 


			$BlacSol = new BlacsolController(
				$data[0]['username'],	// username
				$data[0]['imei'],	// imei
				$data[0]['latitude'],	// latitude
				$data[0]['longitude'],	// longitude
				$data[0]['altitude'],	// altitude
				$data[0]['speed'],	// speed
				$data[0]['azimuth'],	// azimuth
				$data[0]['odometer'],	// odometer
				$data[0]['dateTimeUTC']	// dateTimeUTC
			);
			
			$req[] = $BlacSol->PosiCont();


			return response()->json([
				'data'   => $req, 
				'status' => true,
				'code'   => 200
			]);
		} catch (\Exception $th) {
			return response()->json([
				'status' => false,
				'code'   => 500,
				'error'  => $th->getMessage()
			]);
		}
	}

	public function Idle()
	{
		Log::channel()->info('[*]['.date('H:i:s')."] Inicializacion de Peticion de Idle.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			
			for ($i=0; $i < count($data)-1; $i++) { 
				$BlacSol = new BlacsolController(
					$data[$i]['username'],	// username
					$data[$i]['imei'],	// imei
					$data[$i]['latitude'],	// latitude
					$data[$i]['longitude'],	// longitude
					$data[$i]['altitude'],	// altitude
					$data[$i]['speed'],	// speed
					$data[$i]['azimuth'],	// azimuth
					$data[$i]['odometer'],	// odometer
					$data[$i]['dateTimeUTC']	// dateTimeUTC
				);
				
				$BlacSol->Idle();
				unset($BlacSol);
			} 


			return response()->json([
				'status' => true,
				'code'   => 200
			]);
		} catch (\Exception $th) {
			return response()->json([
				'status' => false,
				'code'   => 500,
				'error'  => $th->getMessage()
			]);
		}

	}

	public function Mov()
	{
		Log::channel()->info('[*]['.date('H:i:s')."] Inicializacion de Peticion de Mov.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			
			for ($i=0; $i < count($data)-1; $i++) { 
				$BlacSol = new BlacsolController(
					$data[$i]['username'],	// username
					$data[$i]['imei'],	// imei
					$data[$i]['latitude'],	// latitude
					$data[$i]['longitude'],	// longitude
					$data[$i]['altitude'],	// altitude
					$data[$i]['speed'],	// speed
					$data[$i]['azimuth'],	// azimuth
					$data[$i]['odometer'],	// odometer
					$data[$i]['dateTimeUTC']	// dateTimeUTC
				);
				
				$BlacSol->Mov();
				unset($BlacSol);
			} 


			return response()->json([
				'status' => true,
				'code'   => 200
			]);
		} catch (\Exception $th) {
			return response()->json([
				'status' => false,
				'code'   => 500,
				'error'  => $th->getMessage()
			]);
		}

	}

	public function Detenido()
	{
		Log::channel()->info('[*]['.date('H:i:s')."] Inicializacion de Peticion de Detenido.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			
			for ($i=0; $i < count($data)-1; $i++) { 
				$BlacSol = new BlacsolController(
					$data[$i]['username'],	// username
					$data[$i]['imei'],	// imei
					$data[$i]['latitude'],	// latitude
					$data[$i]['longitude'],	// longitude
					$data[$i]['altitude'],	// altitude
					$data[$i]['speed'],	// speed
					$data[$i]['azimuth'],	// azimuth
					$data[$i]['odometer'],	// odometer
					$data[$i]['dateTimeUTC']	// dateTimeUTC
				);
				
				$BlacSol->Detenido();
				unset($BlacSol);
			} 


			return response()->json([
				'status' => true,
				'code'   => 200
			]);
		} catch (\Exception $th) {
			return response()->json([
				'status' => false,
				'code'   => 500,
				'error'  => $th->getMessage()
			]);
		}

	}

	public function IgnEnc()
	{
		Log::channel()->info('[*]['.date('H:i:s')."] Inicializacion de Peticion de IgnEnc.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			
			for ($i=0; $i < count($data)-1; $i++) { 
				$BlacSol = new BlacsolController(
					$data[$i]['username'],	// username
					$data[$i]['imei'],	// imei
					$data[$i]['latitude'],	// latitude
					$data[$i]['longitude'],	// longitude
					$data[$i]['altitude'],	// altitude
					$data[$i]['speed'],	// speed
					$data[$i]['azimuth'],	// azimuth
					$data[$i]['odometer'],	// odometer
					$data[$i]['dateTimeUTC']	// dateTimeUTC
				);
				
				$BlacSol->IgnEnc();
				unset($BlacSol);
			} 


			return response()->json([
				'status' => true,
				'code'   => 200
			]);
		} catch (\Exception $th) {
			return response()->json([
				'status' => false,
				'code'   => 500,
				'error'  => $th->getMessage()
			]);
		}

	}

	public function IgnApa()
	{
		Log::channel()->info('[*]['.date('H:i:s')."] Inicializacion de Peticion de IgnApa.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			
			for ($i=0; $i < count($data)-1; $i++) { 
				$BlacSol = new BlacsolController(
					$data[$i]['username'],	// username
					$data[$i]['imei'],	// imei
					$data[$i]['latitude'],	// latitude
					$data[$i]['longitude'],	// longitude
					$data[$i]['altitude'],	// altitude
					$data[$i]['speed'],	// speed
					$data[$i]['azimuth'],	// azimuth
					$data[$i]['odometer'],	// odometer
					$data[$i]['dateTimeUTC']	// dateTimeUTC
				);
				
				$BlacSol->IgnApa();
				unset($BlacSol);
			} 


			return response()->json([
				'status' => true,
				'code'   => 200
			]);
		} catch (\Exception $th) {
			return response()->json([
				'status' => false,
				'code'   => 500,
				'error'  => $th->getMessage()
			]);
		}

	}

	public function DetJamm()
	{
		Log::channel()->info('[*]['.date('H:i:s')."] Inicializacion de Peticion de DetJamm.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			
			for ($i=0; $i < count($data)-1; $i++) { 
				$BlacSol = new BlacsolController(
					$data[$i]['username'],	// username
					$data[$i]['imei'],	// imei
					$data[$i]['latitude'],	// latitude
					$data[$i]['longitude'],	// longitude
					$data[$i]['altitude'],	// altitude
					$data[$i]['speed'],	// speed
					$data[$i]['azimuth'],	// azimuth
					$data[$i]['odometer'],	// odometer
					$data[$i]['dateTimeUTC']	// dateTimeUTC
				);
				
				$BlacSol->DetJamm();
				unset($BlacSol);
			} 


			return response()->json([
				'status' => true,
				'code'   => 200
			]);
		} catch (\Exception $th) {
			return response()->json([
				'status' => false,
				'code'   => 500,
				'error'  => $th->getMessage()
			]);
		}

	}

	public function DesconBat()
	{
		Log::channel()->info('[*]['.date('H:i:s')."] Inicializacion de Peticion de DesconBat.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			
			for ($i=0; $i < count($data)-1; $i++) { 
				$BlacSol = new BlacsolController(
					$data[$i]['username'],	// username
					$data[$i]['imei'],	// imei
					$data[$i]['latitude'],	// latitude
					$data[$i]['longitude'],	// longitude
					$data[$i]['altitude'],	// altitude
					$data[$i]['speed'],	// speed
					$data[$i]['azimuth'],	// azimuth
					$data[$i]['odometer'],	// odometer
					$data[$i]['dateTimeUTC']	// dateTimeUTC
				);
				
				$BlacSol->DesconBat();
				unset($BlacSol);
			} 


			return response()->json([
				'status' => true,
				'code'   => 200
			]);
		} catch (\Exception $th) {
			return response()->json([
				'status' => false,
				'code'   => 500,
				'error'  => $th->getMessage()
			]);
		}

	}

	public function ReconBat()
	{
		Log::channel()->info('[*]['.date('H:i:s')."] Inicializacion de Peticion de ReconBat.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			
			for ($i=0; $i < count($data)-1; $i++) { 
				$BlacSol = new BlacsolController(
					$data[$i]['username'],	// username
					$data[$i]['imei'],	// imei
					$data[$i]['latitude'],	// latitude
					$data[$i]['longitude'],	// longitude
					$data[$i]['altitude'],	// altitude
					$data[$i]['speed'],	// speed
					$data[$i]['azimuth'],	// azimuth
					$data[$i]['odometer'],	// odometer
					$data[$i]['dateTimeUTC']	// dateTimeUTC
				);
				
				$BlacSol->ReconBat();
				unset($BlacSol);
			} 


			return response()->json([
				'status' => true,
				'code'   => 200
			]);
		} catch (\Exception $th) {
			return response()->json([
				'status' => false,
				'code'   => 500,
				'error'  => $th->getMessage()
			]);
		}

	}
}