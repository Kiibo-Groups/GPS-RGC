<?php

namespace App\Http\Controllers\api;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AVLController;
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
use Illuminate\Support\Facades\Cache;
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
			'webhook_rgc_csv',
			'SetPulseAVL',
			'DecodePacket'
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
		 * "length" => 970,
		 * "crc" => "5F9F",
		 * "crc_status" => "CRC check passed",
		 * "imei" => 861773070038757,
		 * "command_id" => 68,
		 * "timestamp" => "2025-01-23T06:59:27+00:00",
		 * "priority" => 0,
		 * "longitude" => -214.7483648,
		 * "latitude" => -214.7483648,
		 * "altitude" => -3276.8,
		 * "angle" => 655.35,
		 * "satellites" => 255,
		 * "speed" => 65535,
		 * "hdop" => 25.5,
		 * "event_io" => 7,
		 * "status" => "OK",
		 * "status_code" => 200
		 * }
		 */
		// Push to Event
		$pusher = new pusher("8442d369ae2137d24bf4", "ff80680a66895a936bd1", "1934866", array('cluster' => 'us3'));

		// $paqueteHex = $request->input('packet');
	
		// if (empty($paqueteHex)) {
		// 	return response()->json([
		// 		'status' => 400,
		// 		'message' => 'Packet vacio o no recibido.'
		// 	], 400);
		// }

		// $parser = new PacketParserService(json_encode($paqueteHex));
		// $datos = $parser->parse();

		$datos = $request->all();
		Log::info('[*][' . date('H:i:s') . "] Data Decifrada: " . json_encode($datos));

		// Verificar que tenga IMEI
		if (empty($datos['imei'])) {
			return response()->json([
				'status' => 400,
				'message' => 'IMEI_not_found'
			], 400);
		}

		// [
		// 	"Timestamp" => "2025-07-05T17:25:53.000Z",
		// 	"Longitude" => -100.250153299999993805613485164940357208251953125,
		// 	"Latitude" => 25.577844999999999942019712761975824832916259765625,
		// 	"Altitude" => 0.000600900000000000023524238113026285645901225507259368896484375,
		// 	"Angle" => 280.80000000000001136868377216160297393798828125,
		// 	"Satellites" => 22,
		// 	"Speed" => 17
		// ],
		// [
		// 	"Timestamp" => "2025-07-05T17:25:54.000Z",
		// 	"Longitude" => -100.25020000000000663931132294237613677978515625,
		// 	"Latitude" => 25.577844999999999942019712761975824832916259765625,
		// 	"Altitude" => 0.000601199999999999976606213092367170247598551213741302490234375,
		// 	"Angle" => 259.6000000000000227373675443232059478759765625,
		// 	"Satellites" => 22,
		// 	"Speed" => 15
		// ],
		// [
		// 	"Timestamp" => "2025-07-05T17:25:55.000Z",
		// 	"Longitude" => -100.2502400000000051250026444904506206512451171875,
		// 	"Latitude" => 25.57782999999999873352862778119742870330810546875,
		// 	"Altitude" => 0.000601599999999999986328991230521978650358505547046661376953125,
		// 	"Angle" => 240.900000000000005684341886080801486968994140625,
		// 	"Satellites" => 22,
		// 	"Speed" => 15
		// ],
		// [
		// 	"Timestamp" => "2025-07-05T17:25:58.000Z",
		// 	"Longitude" => -100.2503315999999955465682432986795902252197265625,
		// 	"Latitude" => 25.577764999999999417923390865325927734375,
		// 	"Altitude" => 0.000601700000000000042969794389335902451421134173870086669921875,
		// 	"Angle" => 220.900000000000005684341886080801486968994140625,
		// 	"Satellites" => 21,
		// 	"Speed" => 13
		// ],
		// [
		// 	"Timestamp" => "2025-07-05T17:26:01.000Z",
		// 	"Longitude" => -100.2504315999999988662239047698676586151123046875,
		// 	"Latitude" => 25.57769499999999851524989935569465160369873046875,
		// 	"Altitude" => 0.00059840000000000001696698337383395482902415096759796142578125,
		// 	"Angle" => 243.900000000000005684341886080801486968994140625,
		// 	"Satellites" => 21,
		// 	"Speed" => 15
		// ],
		// [
		// 	"Timestamp" => "2025-07-05T17:26:06.000Z",
		// 	"Longitude" => -100.2506000000000057070792536251246929168701171875,
		// 	"Latitude" => 25.577586600000000061072569224052131175994873046875,
		// 	"Altitude" => 0.000596300000000000020132506772796432414907030761241912841796875,
		// 	"Angle" => 228,
		// 	"Satellites" => 21,
		// 	"Speed" => 19
		// ],
		// [
		// 	"Timestamp" => "2025-07-05T17:26:13.000Z",
		// 	"Longitude" => -100.2509150000000062163962866179645061492919921875,
		// 	"Latitude" => 25.57731499999999869032762944698333740234375,
		// 	"Altitude" => 0.00059559999999999994890753640675029600970447063446044921875,
		// 	"Angle" => 208.69999999999998863131622783839702606201171875,
		// 	"Satellites" => 21,
		// 	"Speed" => 15
		// ],
		// [
		// 	"Timestamp" => "2025-07-05T17:26:14.000Z",
		// 	"Longitude" => -100.2509165999999964924427331425249576568603515625,
		// 	"Latitude" => 25.577276600000001138823790824972093105316162109375,
		// 	"Altitude" => 0.000595799999999999953768925475827700211084447801113128662109375,
		// 	"Angle" => 165.599999999999994315658113919198513031005859375,
		// 	"Satellites" => 22,
		// 	"Speed" => 14
		// ],


		// "positions":[
		// 	{"Timestamp":"2025-07-05T17:25:53.000Z","Longitude":-100.250153299999993805613485164940357208251953125,"Latitude":25.577844999999999942019712761975824832916259765625,"Altitude":0.000600900000000000023524238113026285645901225507259368896484375,"Angle":280.80000000000001136868377216160297393798828125,"Satellites":22,"Speed":17},
		// 	{"Timestamp":"2025-07-05T17:25:54.000Z","Longitude":-100.25020000000000663931132294237613677978515625,"Latitude":25.577844999999999942019712761975824832916259765625,"Altitude":0.000601199999999999976606213092367170247598551213741302490234375,"Angle":259.6000000000000227373675443232059478759765625,"Satellites":22,"Speed":15},
		// 	{"Timestamp":"2025-07-05T17:25:55.000Z","Longitude":-100.2502400000000051250026444904506206512451171875,"Latitude":25.57782999999999873352862778119742870330810546875,"Altitude":0.000601599999999999986328991230521978650358505547046661376953125,"Angle":240.900000000000005684341886080801486968994140625,"Satellites":22,"Speed":15},
		// 	{"Timestamp":"2025-07-05T17:25:58.000Z","Longitude":-100.2503315999999955465682432986795902252197265625,"Latitude":25.577764999999999417923390865325927734375,"Altitude":0.000601700000000000042969794389335902451421134173870086669921875,"Angle":220.900000000000005684341886080801486968994140625,"Satellites":21,"Speed":13},
		// 	{"Timestamp":"2025-07-05T17:26:01.000Z","Longitude":-100.2504315999999988662239047698676586151123046875,"Latitude":25.57769499999999851524989935569465160369873046875,"Altitude":0.00059840000000000001696698337383395482902415096759796142578125,"Angle":243.900000000000005684341886080801486968994140625,"Satellites":21,"Speed":15},
		// 	{"Timestamp":"2025-07-05T17:26:06.000Z","Longitude":-100.2506000000000057070792536251246929168701171875,"Latitude":25.577586600000000061072569224052131175994873046875,"Altitude":0.000596300000000000020132506772796432414907030761241912841796875,"Angle":228,"Satellites":21,"Speed":19},
		// 	{"Timestamp":"2025-07-05T17:26:13.000Z","Longitude":-100.2509150000000062163962866179645061492919921875,"Latitude":25.57731499999999869032762944698333740234375,"Altitude":0.00059559999999999994890753640675029600970447063446044921875,"Angle":208.69999999999998863131622783839702606201171875,"Satellites":21,"Speed":15},
		// 	{"Timestamp":"2025-07-05T17:26:14.000Z","Longitude":-100.2509165999999964924427331425249576568603515625,"Latitude":25.577276600000001138823790824972093105316162109375,"Altitude":0.000595799999999999953768925475827700211084447801113128662109375,"Angle":165.599999999999994315658113919198513031005859375,"Satellites":22,"Speed":14}]
			
		// "positions":[
		// 	{"Timestamp":"2025-07-05T17:25:53.000Z","Longitude":-100.250153299999993805613485164940357208251953125,"Latitude":25.577844999999999942019712761975824832916259765625,"Altitude":0.000600900000000000023524238113026285645901225507259368896484375,"Angle":280.80000000000001136868377216160297393798828125,"Satellites":22,"Speed":17},
		// 	{"Timestamp":"2025-07-05T17:25:54.000Z","Longitude":-100.25020000000000663931132294237613677978515625,"Latitude":25.577844999999999942019712761975824832916259765625,"Altitude":0.000601199999999999976606213092367170247598551213741302490234375,"Angle":259.6000000000000227373675443232059478759765625,"Satellites":22,"Speed":15},
		// 	{"Timestamp":"2025-07-05T17:25:55.000Z","Longitude":-100.2502400000000051250026444904506206512451171875,"Latitude":25.57782999999999873352862778119742870330810546875,"Altitude":0.000601599999999999986328991230521978650358505547046661376953125,"Angle":240.900000000000005684341886080801486968994140625,"Satellites":22,"Speed":15},
		// 	{"Timestamp":"2025-07-05T17:25:58.000Z","Longitude":-100.2503315999999955465682432986795902252197265625,"Latitude":25.577764999999999417923390865325927734375,"Altitude":0.000601700000000000042969794389335902451421134173870086669921875,"Angle":220.900000000000005684341886080801486968994140625,"Satellites":21,"Speed":13},
		// 	{"Timestamp":"2025-07-05T17:26:01.000Z","Longitude":-100.2504315999999988662239047698676586151123046875,"Latitude":25.57769499999999851524989935569465160369873046875,"Altitude":0.00059840000000000001696698337383395482902415096759796142578125,"Angle":243.900000000000005684341886080801486968994140625,"Satellites":21,"Speed":15},
		// 	{"Timestamp":"2025-07-05T17:26:06.000Z","Longitude":-100.2506000000000057070792536251246929168701171875,"Latitude":25.577586600000000061072569224052131175994873046875,"Altitude":0.000596300000000000020132506772796432414907030761241912841796875,"Angle":228,"Satellites":21,"Speed":19},
		// 	{"Timestamp":"2025-07-05T17:26:13.000Z","Longitude":-100.2509150000000062163962866179645061492919921875,"Latitude":25.57731499999999869032762944698333740234375,"Altitude":0.00059559999999999994890753640675029600970447063446044921875,"Angle":208.69999999999998863131622783839702606201171875,"Satellites":21,"Speed":15},
		// 	{"Timestamp":"2025-07-05T17:26:14.000Z","Longitude":-100.2509165999999964924427331425249576568603515625,"Latitude":25.577276600000001138823790824972093105316162109375,"Altitude":0.000595799999999999953768925475827700211084447801113128662109375,"Angle":165.599999999999994315658113919198513031005859375,"Satellites":22,"Speed":14}]
			

		$imei = $datos['imei'] ?? $datos['imei'] ?? null;
		$datos['packet'] = $datos['packet'] ?? null; // Asegurarse de que 'packet' esté presente
		$datos['status_code'] = 200; // Default to 200 if not present
		$datos['date_update'] = now();

		$registro = Getgsminfo::where('imei', $imei)->first();

		// Buscar GPS y vehículo actuales
		$gps = GpsDevices::where('uuid_device', $imei)->first();
		$vehiculo = $gps ? vehicle_units::where('gps_devices_id', $gps->id)->first() : null;

		if ($registro) {
			$cambios = [
				'id' 		=> $registro->id,
				'packet'    => $datos['packet'],
				'longitude' => $datos['longitude'] ?? null,
				'latitude'  => $datos['latitude'] ?? null,
				'altitude'  => $datos['altitude'] ?? null,
				'angle'     => $datos['angle'] ?? null,
				'speed'     => $datos['speed'] ?? null,
				'date_update' => $datos['date_update'],
			];

			// Actualiza si se detectan nuevos IDs
			if ($gps && $registro->gps_devices_id !== $gps->id) {
				$cambios['gps_devices_id'] = $gps->id;
			}

			if ($vehiculo && $registro->vehicle_units_id !== $vehiculo->id) {
				$cambios['vehicle_units_id'] = $vehiculo->id;
			}

			$registro->fill($cambios)->save();
			$channels = $pusher->trigger(
				'ruptela-server',
				'coords-gps',
				json_encode($cambios)
			);
		} else {
			if ($gps) {
				$datos['gps_devices_id'] = $gps->id;
				if ($vehiculo) {
					$datos['vehicle_units_id'] = $vehiculo->id;
				}
			}

			Getgsminfo::create($datos);
		}

		return response()->json([
			'status' => 200,
			'message' => 'data_received'
		], 200);
	}

	/**
	 * Obtiene coordenadas GPS en tiempo real de cualquier tipo de paquete
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getRealTimeCoordinates(Request $request)
	{
		$paqueteHex = $request->input('packet');
		
		if (empty($paqueteHex)) {
			return response()->json([
				'status' => 400,
				'message' => 'Packet vacío o no recibido.'
			], 400);
		}

		try {
			$parser = new PacketParserService(json_encode($paqueteHex));
			$coords = $parser->getRealTimeCoordinates();

			if (isset($coords['error'])) {
				return response()->json([
					'status' => 400,
					'message' => $coords['error']
				], 400);
			}

			// Log para debugging
			Log::info('[*][' . date('H:i:s') . "] Coordenadas en tiempo real: " . json_encode($coords));

			return response()->json([
				'status' => 'success',
				'data' => $coords,
				'message' => 'Coordenadas obtenidas exitosamente'
			], 200);

		} catch (\Exception $e) {
			Log::error('Error procesando coordenadas: ' . $e->getMessage());
			return response()->json([
				'status' => 500,
				'message' => 'Error procesando el paquete: ' . $e->getMessage()
			], 500);
		}
	}

	/**
	 * Obtiene coordenadas como string simple (lat,lon)
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getCoordinatesString(Request $request)
	{
		$paqueteHex = $request->input('packet');
		
		if (empty($paqueteHex)) {
			return response()->json([
				'status' => 400,
				'message' => 'Packet vacío o no recibido.'
			], 400);
		}

		try {
			$parser = new PacketParserService(json_encode($paqueteHex));
			$coordsString = $parser->getCoordinatesString();

			if ($coordsString === 'error') {
				return response()->json([
					'status' => 400,
					'message' => 'No se pudieron extraer coordenadas del paquete'
				], 400);
			}

			return response()->json([
				'status' => 'success',
				'coordinates' => $coordsString,
				'message' => 'Coordenadas obtenidas'
			], 200);

		} catch (\Exception $e) {
			Log::error('Error procesando coordenadas: ' . $e->getMessage());
			return response()->json([
				'status' => 500,
				'message' => 'Error procesando el paquete: ' . $e->getMessage()
			], 500);
		}
	}

	public function DecodePacket()
	{
		$hexPacket = '03d300030fc72db776e5440108685c4203000000c43eb53b0f3f6a8b176f7a1c0e003b0700081001990100823b0086000087';
    
		
		$parser = new PacketParserService(json_encode($hexPacket));
		 

		$binaryPacket = $parser->hexToBinaryString($hexPacket);
		$decoded = $parser->parseRuptelaRecordHeader($hexPacket);
		
		return response()->json([
			'status' => 200,
			'message' => 'data_received',
			'data' => $decoded,
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

		$getAll = Getgsminfo::where('gps_devices_id','!=', null)->with('getGPS', 'getVehicle')->get([
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

		return response()->json([
			'devices' => $devices
		]);
	}

	public function SetPulseAVL()
	{
		try {
			$gsmInfo = Getgsminfo::where('status_code', 200)->get();
			$avlController = new AVLController(new \App\Services\AVLService());

			foreach ($gsmInfo as $datos) {
				// Buscar GPS y vehículo actuales
				$gps = GpsDevices::where('uuid_device', $datos['imei'])->first();
				$vehiculo = $gps ? vehicle_units::where('gps_devices_id', $gps->id)->first() : null;
				
				$eventoAVL = [
					'altitude' => $datos['altitude'] ?? 0,
					'asset' => $gps->name_device ?? 'Unknown',
					'battery' => 100,
					'code' => $datos['event_io'] ?? '1',
					'course' => $datos['angle'] ?? 0,
					'customer' => [
						'id' => '0',
						'name' => 'SALGO FREIGHT LOGISTICS'
					],
					'date' => now()->format('Y-m-d\TH:i:s'),
					'direction' => $datos['angle'] > 0 ? 'Norte' : 'Desconocido',
					'humidity' => 75.5,
					'ignition' => true,
					'latitude' => $datos['latitude'] ?? 0,
					'longitude' => $datos['longitude'] ?? 0,
					'odometer' => $datos['odometer'] ?? 0, // Asumiendo que odómetro es opcional
					'serialNumber' => $datos['imei'] ?? 'Unknown',
					'shipment' => '0',
					'speed' => $datos['speed'] ?? 0,
					'temperature' => $datos['temperature'] ?? 32.5, // Asumiendo que la temperatura es opcional
					'vehicleType' => $gps->vehicle_type ?? 'Desconocido',
					'vehicleBrand' => $vehiculo->name_unit ?? 'Desconocido',
					'vehicleModel' => $vehiculo->descript ?? 'Desconocido',
				];
				
				// Enviamos el evento a AVLController
				Log::info('[*][' . date('H:i:s') . "] Pulsasion enviada a AVLCONTROLLER: " . json_encode($eventoAVL));
				$idJob = $avlController->enviarEvento($eventoAVL);
				Log::info('[*][' . date('H:i:s') . "] IDJOB Obtenido: " . json_encode($idJob));
			}

			return response()->json([
				'status' => 200,
				'message' => 'Pulsasiones enviadas correctamente',
				'data'  => 'IDJOB Obtenido: ' . json_encode(($idJob))
			]);
		} catch (\Exception $th) {
			// Decodifica error SOAP si es posible
            if (str_contains($th->getMessage(), 'oken no es valido')) {
                Cache::forget('avl_token'); // limpia token si es inválido
            }
			return response()->json([
				'status' => 500,
				'message' => $th->getMessage()
			], 500);
		}
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
