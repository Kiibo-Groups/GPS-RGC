<?php

namespace App\Http\Controllers;

use App\Http\Controllers\{BlacsolController};
use App\Helper;
use App\Models\Blacvehicles; 
use Cookie;
use DateTime;

class SamsaraController {

    const SamsaraToken  = "samsara_api_D3UNtaugI9PijhLI8xBWOUelx7xyHU";
    const GetAllDrivers = "https://api.samsara.com/fleet/drivers";
    const GetFleetsStat = "https://api.samsara.com/fleet/vehicles/stats/feed?type=fuelPercents";
    const GetAllAddress = "https://api.samsara.com/addresses";
    const GetLocations  = "https://api.samsara.com/fleet/vehicles/locations";
    const GetVehicles   = "https://api.samsara.com/fleet/vehicles/stats/feed?types=gps,engineStates,gpsOdometerMeters"; // vehicleIds=281474983036551&
    const engineImmobilizer   = "https://api.samsara.com/fleet/vehicles/stats/feed?types=engineStates&decorations=gps";
    
    public function __construct()
    {

    }

    public function GetAllVehicleAssignments()
    {
        try { 
            $response = $this->GetCurlPetition(self::GetVehicles);
            $req = json_decode($response, true);
            $data = $this->PosiCont($req);
            return $data;

        } catch (\Exception $th) {
            return "Error : ". $th->getMessage();
        }
    }

    public function PosiCont()
    {
        try {
            $response = $this->GetCurlPetition(self::GetVehicles);
            
            $req = json_decode($response, true);
            $data = [];
            $x = 0;

            foreach ($req['data'] as $key) {
                if ($key['gps'][0]['isEcuSpeed'] == true && $key['engineStates'][0]['value'] == 'On') {
                    $data[] = [ 
                        'username'  => $key['name'],
                        "imei"      => $key['id'],
                        "latitude"  => $key['gps'][0]['latitude'],
                        "longitude" => $key['gps'][0]['longitude'],
                        "altitude"  => "0.00",
                        'engineStates' => $key['engineStates'],
                        "speed"     => $key['gps'][0]['speedMilesPerHour'],
                        "azimuth"   => "0",
                        "odometer"  => (isset($key['gpsOdometerMeters'])) ? ($key['gpsOdometerMeters'][0]['value'] / 100) : '0.00',
                        "dateTimeUTC" => date('YmdHis')
                    ];
 
                }
            }

            return $data;
        } catch (\Exception $th) {
            return "Error : ". $th->getMessage();
        }
    }

    public function Idle()
    {
        try {
            
            $response = $this->GetCurlPetition(self::GetVehicles);
            
            $req = json_decode($response, true);
            $data = [];
            $x = 0;

            foreach ($req['data'] as $key) {
                if ((isset($key['engineStates'])) && $key['engineStates'][0]['value'] == 'Idle') {
                    $data[] = [ 
                        'username'  => $key['name'],
                        "imei"      => $key['id'],
                        "latitude"  => $key['gps'][0]['latitude'],
                        "longitude" => $key['gps'][0]['longitude'],
                        "altitude"  => "0.00",
                        'engineStates' => $key['engineStates'],
                        "speed"     => $key['gps'][0]['speedMilesPerHour'],
                        "azimuth"   => "0",
                        "odometer"  => (isset($key['gpsOdometerMeters'])) ? ($key['gpsOdometerMeters'][0]['value'] / 100) : '0.00',
                        "dateTimeUTC" => date('YmdHis')
                    ];
                }
            }

            return $data;
        } catch (\Exception $th) {
            return "Error : ". $th->getMessage();
        }
    }
 
    public function Mov()
    {
        try {
            
            $response = $this->GetCurlPetition(self::GetVehicles);
            
            $req = json_decode($response, true);
            $data = [];
            $x = 0;

            foreach ($req['data'] as $key) {
                if ((isset($key['engineStates'])) && $key['engineStates'][0]['value'] == 'On' && $key['gps'][0]['speedMilesPerHour'] > 0 ) {
                    $data[] = [
                        'username'  => $key['name'],
                        "imei"      => $key['id'],
                        "latitude"  => $key['gps'][0]['latitude'],
                        "longitude" => $key['gps'][0]['longitude'],
                        "altitude"  => "0.00",
                        'engineStates' => $key['engineStates'],
                        "speed"     => $key['gps'][0]['speedMilesPerHour'],
                        "azimuth"   => "0",
                        "odometer"  => (isset($key['gpsOdometerMeters'])) ? ($key['gpsOdometerMeters'][0]['value'] / 100) : '0.00',
                        "dateTimeUTC" => date('YmdHis')
                    ];
                }
            }

            return $data;
        } catch (\Exception $th) {
            return "Error : ". $th->getMessage();
        }
    }

    public function Detenido()
    {
        try {
            $response = $this->GetCurlPetition(self::GetVehicles);
            
            $req = json_decode($response, true);
            $data = [];
            $x = 0;

            foreach ($req['data'] as $key) {
                if ((isset($key['engineStates'])) && $key['engineStates'][0]['value'] == 'Off') {
                    $data[] = [ 
                        'username'  => $key['name'],
                        "imei"      => $key['id'],
                        "latitude"  => $key['gps'][0]['latitude'],
                        "longitude" => $key['gps'][0]['longitude'],
                        "altitude"  => "0.00",
                        'engineStates' => $key['engineStates'],
                        "speed"     => $key['gps'][0]['speedMilesPerHour'],
                        "azimuth"   => "0",
                        "odometer"  => (isset($key['gpsOdometerMeters'])) ? ($key['gpsOdometerMeters'][0]['value'] / 100) : '0.00',
                        "dateTimeUTC" => date('YmdHis')
                    ];
                }
            }

            return $data;
        } catch (\Exception $th) {
            return "Error : ". $th->getMessage();
        }
    }

    public function ChkIgn()
    {
        // try {
            $response = $this->GetCurlPetition(self::GetVehicles);
            $chkDb    = new Blacvehicles;
            
            $req = json_decode($response, true);

            $data = [];
            $IgnApa = [];
            $IgnEnc = [];

            foreach ($req['data'] as $key) {

                $chkID = Blacvehicles::where('imei', $key['id'])->first();

                if (isset($chkID->id)) { // Este vehiculo existe y ya ha sido enviado
                    if ((isset($key['engineStates'])) && $key['engineStates'][0]['value'] != $chkID->engineStates) { // hay cambios
                        if ($key['engineStates'][0]['value'] == 'Off') { // Ign Apag
                            $this->IgnApa([ 
                                'username'  => $key['name'],
                                "imei"      => $key['id'],
                                "latitude"  => $key['gps'][0]['latitude'],
                                "longitude" => $key['gps'][0]['longitude'],
                                "altitude"  => "0.00",
                                'engineStates' => $key['engineStates'],
                                "speed"     => $key['gps'][0]['speedMilesPerHour'],
                                "azimuth"   => "0",
                                "odometer"  => (isset($key['gpsOdometerMeters'])) ? ($key['gpsOdometerMeters'][0]['value'] / 100) : '0.00',
                                "dateTimeUTC" => date('YmdHis')
                            ]); // Enviamos la

                            $IgnApa[] = [ 
                                'username'  => $key['name'],
                                "imei"      => $key['id'],
                                "latitude"  => $key['gps'][0]['latitude'],
                                "longitude" => $key['gps'][0]['longitude'],
                                "altitude"  => "0.00",
                                'engineStates' => $key['engineStates'],
                                "speed"     => $key['gps'][0]['speedMilesPerHour'],
                                "azimuth"   => "0",
                                "odometer"  => (isset($key['gpsOdometerMeters'])) ? ($key['gpsOdometerMeters'][0]['value'] / 100) : '0.00',
                                "dateTimeUTC" => date('YmdHis')
                            ];

                            // Cambiamos el Status en la DB
                            $chkID->engineStates = "Off";
                            $chkID->save();

                        }elseif ($key['engineStates'][0]['value'] == 'On') { // Ign Enc
                            $this->IgnEnc([ 
                                'username'  => $key['name'],
                                "imei"      => $key['id'],
                                "latitude"  => $key['gps'][0]['latitude'],
                                "longitude" => $key['gps'][0]['longitude'],
                                "altitude"  => "0.00",
                                'engineStates' => $key['engineStates'],
                                "speed"     => $key['gps'][0]['speedMilesPerHour'],
                                "azimuth"   => "0",
                                "odometer"  => (isset($key['gpsOdometerMeters'])) ? ($key['gpsOdometerMeters'][0]['value'] / 100) : '0.00',
                                "dateTimeUTC" => date('YmdHis')
                            ]); // Enviamos la

                            $IgnEnc[] = [ 
                                'username'  => $key['name'],
                                "imei"      => $key['id'],
                                "latitude"  => $key['gps'][0]['latitude'],
                                "longitude" => $key['gps'][0]['longitude'],
                                "altitude"  => "0.00",
                                'engineStates' => $key['engineStates'],
                                "speed"     => $key['gps'][0]['speedMilesPerHour'],
                                "azimuth"   => "0",
                                "odometer"  => (isset($key['gpsOdometerMeters'])) ? ($key['gpsOdometerMeters'][0]['value'] / 100) : '0.00',
                                "dateTimeUTC" => date('YmdHis')
                            ];

                            // Cambiamos el Status en la DB
                            $chkID->engineStates = "On";
                            $chkID->save();
                        }
                    }
                }else {;
                    $chkDb->create([ 
                        'username'  => $key['name'],
                        "imei"      => $key['id'],
                        "latitude"  => $key['gps'][0]['latitude'],
                        "longitude" => $key['gps'][0]['longitude'],
                        "altitude"  => "0.00",
                        'engineStates' => (isset($key['engineStates'])) ? $key['engineStates'][0]['value'] : 'Off',
                        "speed"     => $key['gps'][0]['speedMilesPerHour'],
                        "azimuth"   => "0",
                        "odometer"  => (isset($key['gpsOdometerMeters'])) ? ($key['gpsOdometerMeters'][0]['value'] / 100) : '0.00', 
                    ]); // Creamos....
                }
            }


            return [
                'IgnApa' => $IgnApa,
                'IgnEnc' => $IgnEnc,
                'data'   => $req
            ];
        // } catch (\Exception $th) {
        //     return "Error : ". $th->getMessage();
        // }
    }

    public function IgnEnc()
    {
        try {
			$BlacSol = new BlacsolController(
                'username',
				'imei',
				'latitude',
				'longitude',
				'altitude',
				'speed',
				'azimuth',
				'odometer',
				'dateTimeUTC',
            );
            
            $req = $BlacSol->IgnEnc();

            return $req;
        } catch (\Exception $th) {
            return "Error : ". $th->getMessage();
        }
    }

    public function IgnApa($data)
    {
        try {
			$BlacSol = new BlacsolController(
                $data['username'],	// username
                $data['imei'],	// imei
                $data['latitude'],	// latitude
                $data['longitude'],	// longitude
                $data['altitude'],	// altitude
                $data['speed'],	// speed
                $data['azimuth'],	// azimuth
                $data['odometer'],	// odometer
                $data['dateTimeUTC']	// dateTimeUTC
            );
            
            $req = $BlacSol->IgnApa();
            return $req;
        } catch (\Exception $th) {
            return "Error : ". $th->getMessage();
        }
    }

    public function GetCurlPetition($url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authorization: Bearer ".self::SamsaraToken
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        }

        return $response;
    }
}