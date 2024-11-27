<?php

namespace App\Http\Controllers;

use App\Helper; 

use Cookie;
use DateTime;

class SamsaraController {

    const SamsaraToken  = "samsara_api_D3UNtaugI9PijhLI8xBWOUelx7xyHU";
    const GetAllDrivers = "https://api.samsara.com/fleet/drivers";
    const GetAllAddress = "https://api.samsara.com/addresses";
    const GetLocations  = "https://api.samsara.com/fleet/vehicles/locations";
    const GetVehicles   = "https://api.samsara.com/fleet/vehicles/stats?vehicleIds=281474977607689&types=gps";

    public function __construct()
    {

    }

    public function GetAllVehicleAssignments()
    {
        try {
            
            $response = $this->GetCurlPetition(self::GetVehicles);
            
            $req = json_decode($response, true);
            $data = [];
            $x = 0;

            foreach ($req['data'] as $key) {   
                $data[] = [ 
                    'username'  => $key['name'],
                    "imei"      => $key['id'],
                    "latitude"  => $key['gps']['latitude'],
                    "longitude" => $key['gps']['longitude'],
                    "altitude"  => "0.00",
                    "speed"     => $key['gps']['speedMilesPerHour'],
                    "azimuth"   => "0",
                    "odometer"  => "0",
                    "dateTimeUTC" => date('YmdHis')
                ];
                // if ($x <= 5) {
                //     $data[] = [ 
                //         'username'  => $key['name'],
                //         "imei"      => $key['id'],
                //         "latitude"  => $key['gps']['latitude'],
                //         "longitude" => $key['gps']['longitude'],
                //         "altitude"  => "0.00",
                //         "speed"     => $key['gps']['speedMilesPerHour'],
                //         "azimuth"   => "0",
                //         "odometer"  => "0",
                //         "dateTimeUTC" => date('YmdHis')
                //     ];
                // }
                // $x++;
                
            }

            return $data;

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