<?php

namespace App\Http\Controllers\api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Providers\SocketServer;
use App\Http\Controllers\{BlacsolController, SamsaraController};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use App\Services\PacketParserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;
use Redirect;
use App\Models\{User, GpsDevices, vehicle_units, Getgsminfo, Rutas};


// Pusher to Ruptela Services
use App\Events\RuptelaServer;
use Pusher\Pusher;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Maatwebsite\Excel\Facades\Excel;

class ApiController  extends Controller
{
	public function __construct()
	{
		$this->middleware('authApi:api', ['except' => [
			'getToken',
			'PosiCont',
			'Idle',
			'Mov',
			'Detenido',
			'setPusher',
			'ChkIgn',
			'IgnEnc',
			'IgnApa',
			'DetJamm',
			'DesconBat',
			'ReconBat',
			'getGSMInfo',
			'getAllDispositives',
			'webhook_rgc_csv'
		]]);
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
			return response()->json(['status' => 'ERROR', 'code' => 500, 'message' => $th->getMessage()], 500);
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

			// return response()->json($data, 200);
			return response()->json([
				'message' => "informacion_recibida",
				'status' => "success",
				"code" => 200
			], 200);
		} catch (\Exception $th) {
			return response()->json([
				'data' => [],
				'message' => $th->getMessage(),
				'status' => "FAILE",
				"code" => 500
			]);
		}
	}

	public function webhook_rgc_csv(Request $request)
	{
		try {
			$data = $request->all();
			if (isset($data['file'])) {

				$array = Excel::toArray(new Rutas, $data['file']);
				$i = 0;
				$input = [];
				foreach ($array[0] as $a) {
					$i++;
					if ($i > 1) {
						$input['unidad'] 		= $a[0];
						$input['destino'] 		= $a[1];
						$input['origen'] 		= $a[2];
						$input['operador'] 		= $a[3];
						$input['remitente'] 	= $a[4];
						$input['destinatario'] 	= $a[5];
						$input['remision_num'] 	= $a[6];
						$input['caja_num'] 		= $a[7];

						$lims_new_rutas  = new Rutas;
						$lims_new_rutas->create($input);
					}
				}
				// return response()->json($data, 200);
				return response()->json([
					'message' => "informacion_recibida",
					'status' => "success",
					"code" => 200
				], 200);
			} else {
				return response()->json([
					'data' => $request->all(),
					'message' => "file_not_found",
					'status' => "FAILE",
					"code" => 500
				]);
			}
		} catch (\Exception $th) {
			return response()->json([
				'data' => [],
				'message' => $th->getMessage(),
				'status' => "FAILE",
				"code" => 500
			]);
		}
	}

	public function getGSMInfo(Request $request)
	{
		/**
		 * DataPacket Recibido
		 * {
		 * "length":970,
		 * "crc":"5F9F",
		 * "crc_status":"CRC check passed",
		 * "imei":861773070038757,
		 * "command_id":68,
		 * "timestamp":"2025-01-23T06:59:27+00:00",
		 * "priority":0,
		 * "longitude":-214.7483648,
		 * "latitude":-214.7483648,
		 * "altitude":-3276.8,
		 * "angle":655.35,
		 * "satellites":255,
		 * "speed":65535,
		 * "hdop":25.5,
		 * "event_io":7,
		 * "status":"OK",
		 * "status_code":200
		 * }
		 */

		$paqueteHex = $request->input('packet');
		if (empty($paqueteHex)) {
			return response()->json([
				'status' => 400,
				'message' => 'Packet vacío o no recibido.'
			], 400);
		}

		$parser = new PacketParserService(json_encode($paqueteHex));
		$datos = $parser->parse();

		Log::info('[*][' . date('H:i:s') . "] Data Decifrada: " . json_encode($datos));

		// Verificar que tenga IMEI
		if (empty($datos['imei'])) {
			return response()->json([
				'status' => 400,
				'message' => 'IMEI_not_found'
			], 400);
		}

		$imei = $datos['imei'];
		$datos['packet'] = $paqueteHex;
		$datos['date_update'] = now();

		$registroExistente = Getgsminfo::where('imei', $imei)->first();
		if ($registroExistente) {
			$registroExistente->fill([
				'packet'    => $datos['packet'],
				'longitude' => $datos['longitude'] ?? null,
				'latitude'  => $datos['latitude'] ?? null,
				'altitude'  => $datos['altitude'] ?? null,
				'angle'     => $datos['angle'] ?? null,
				'speed'     => $datos['speed'] ?? null,
				'date_update' => $datos['date_update'],
			])->save();
		} else {
			// Buscar dispositivo GPS
			$gps = GpsDevices::where('uuid_device', $imei)->first();
			if ($gps) {
				$datos['gps_devices_id'] = $gps->id;

				// Buscar vehículo asociado al dispositivo
				$vehiculo = vehicle_units::where('gps_devices_id', $gps->id)->first();
				if ($vehiculo) {
					$datos['vehicle_units_id'] = $vehiculo->id;
				}
			}

			Getgsminfo::create($datos);
		}

		return response()->json([
			'status' => 200,
			'message' => 'data_received'
		]);
	}

	public function setPusher()
	{
		// Push to Event
		$pusher = new pusher("8442d369ae2137d24bf4", "ff80680a66895a936bd1", "1934866", array('cluster' => 'us3'));

		$channels = $pusher->trigger(
			'ruptela-server',
			'coords-gps',
			'update_coords'
		);

		return response()->json([
			'data' => 'data_receibed'
		]);
	}

	public function getAllDispositives()
	{

		$getAll = Getgsminfo::with('getGPS', 'getVehicle')->get([
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
		$devices = collect($getAll);

		return response()->json([
			'devices' => $devices
		]);
	}


	/**
	 * Conexion con el WebService
	 * BlackSoul
	 */
	public function PosiCont()
	{
		Log::channel()->info('[*][' . date('H:i:s') . "] Inicializacion de Peticion de PosiCont.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->PosiCont();
			$req  = [];

			for ($i = 0; $i < count($data); $i++) {
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

				$req[] = $BlacSol->PosiCont();
				unset($BlacSol);
			}

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
		Log::channel()->info('[*][' . date('H:i:s') . "] Inicializacion de Peticion de Idle.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->Idle();
			$req = [];

			for ($i = 0; $i < count($data); $i++) {
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

				$req[] = $BlacSol->Idle();
				unset($BlacSol);
			}


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

	public function Mov()
	{
		Log::channel()->info('[*][' . date('H:i:s') . "] Inicializacion de Peticion de Mov.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->Mov();
			$req = [];

			for ($i = 0; $i < count($data); $i++) {
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

				$req[] = $BlacSol->Mov();
				unset($BlacSol);
			}


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

	public function Detenido()
	{
		Log::channel()->info('[*][' . date('H:i:s') . "] Inicializacion de Peticion de Detenido.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->Detenido();
			$req = [];

			for ($i = 0; $i < count($data); $i++) {
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

				$req[] = $BlacSol->Detenido();
				unset($BlacSol);
			}


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

	public function ChkIgn()
	{
		Log::channel()->info('[*][' . date('H:i:s') . "] Inicializacion de Peticion de IgnEnc.... .\r\n");
		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->ChkIgn();
			return response()->json([
				'data'   => $data,
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
		Log::channel()->info('[*][' . date('H:i:s') . "] Inicializacion de Peticion de IgnApa.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->IgnApa([
				'username' => '',
				'imei' => '',
				'latitude' => '',
				'longitude' => '',
				'altitude' => '',
				'speed' => '',
				'azimuth' => '',
				'odometer' => '',
				'dateTimeUTC' => '',
			]);
			// $req  = [];

			// for ($i=0; $i < count($data); $i++) { 
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

			// 	$req[] = $BlacSol->IgnApa();
			// 	unset($BlacSol);
			// } 


			return response()->json([
				'data'   => $data,
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
		Log::channel()->info('[*][' . date('H:i:s') . "] Inicializacion de Peticion de DetJamm.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			$req = [];

			for ($i = 0; $i < count($data); $i++) {
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

				$req[] = $BlacSol->DetJamm();
				unset($BlacSol);
			}


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

	public function DesconBat()
	{
		Log::channel()->info('[*][' . date('H:i:s') . "] Inicializacion de Peticion de DesconBat.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			$req = [];

			for ($i = 0; $i < count($data); $i++) {
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

				$req[] = $BlacSol->DesconBat();
				unset($BlacSol);
			}


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

	public function ReconBat()
	{
		Log::channel()->info('[*][' . date('H:i:s') . "] Inicializacion de Peticion de ReconBat.... .\r\n");

		try {
			$Samsara = new SamsaraController;
			$data = $Samsara->GetAllVehicleAssignments();
			$req = [];
			for ($i = 0; $i < count($data); $i++) {
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

				$req[] = $BlacSol->ReconBat();
				unset($BlacSol);
			}


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
}
