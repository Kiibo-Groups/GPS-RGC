<?php

namespace App\Http\Controllers;

use App\Helper; 

use Cookie;

class BlacsolController {

    const URLGetToken = "http://n2.ws.blacsol.com/track/v2/devices/get/token"; 
    const URLGetEvent = "http://n2.ws.blacsol.com/track/v2/devices/set/Event";
    const SamsaraToken = "samsara_api_D3UNtaugI9PijhLI8xBWOUelx7xyHU";
    private $UserName;
    private $Password;
    private $Token;
    private $json = [
        "username"  => "",
        "imei"      => "",
        "latitude"  => "",
        "longitude" => "",
        "altitude"  => "",
        "speed"     => "",
        "azimuth"   => "",
        "odometer"  => "",
        "dateTimeUTC" => ""
    ];

    public function __construct($username,
        $imei,
        $latitude,
        $longitude,
        $altitude,
        $speed,
        $azimuth,
        $odometer,
        $dateTimeUTC)
    {
        $this->UserName = env('BLACSOL_USERNAME');
        $this->Password = env('BLACSOL_PASSWORD');

        // Si La Cookie de 10 Minutos no Existe Solicitamos Token y guardamos....
        if(!isset($_COOKIE['token_blacsol_ws']))
        {
            $this->CrateToken(); // Solicitamos el Token
        } 

        $this->Token    = env('BLACSOL_TOKEN');

        $this->json = [
            "username"  => $username,
            "imei"      => $imei,
            "latitude"  => $latitude,
            "longitude" => $longitude,
            "altitude"  => $altitude,
            "speed"     => $speed,
            "azimuth"   => $azimuth,
            "odometer"  => $odometer,
            "dateTimeUTC" => $dateTimeUTC
        ];
    }

    /**
     * Posicionamiento Continuo: En cada inicio de carga y hasta el fin del viaje deberá
     * estar transmitiendo al Web Service
     * @return void
     */
    Public function PosiCont()
    {
        try {
            $this->json["username"] = $this->UserName;
            $this->json["event"] = 'PosiCont';

            $req = $this->SendCurlPetition(self::URLGetEvent, $this->json);

            return response()->json([
                'data' => $req,
                'json' => $this->json,
                'code' => 200
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'data' => 'error',
                'error' => $th->getMessage(),
                'code'  => 404
            ]);
        }
    }

    /**
     * IDLE: Cuando la unidad este encendida pero sin velocidad cada 1 min
     * @return void
     */
    public function Idle()
    {
        try {
            $this->json["username"] = $this->UserName;
            $this->json["event"] = 'Idle';
    
            $req = $this->SendCurlPetition(self::URLGetEvent, $this->json);
    
            return response()->json([
                'data' => $req,
                'json' => $this->json,
                'code' => 200
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'data' => 'error',
                'error' => $th->getMessage(),
                'code'  => 404
            ]);
        }
    }

    /**
     * Reporte de Tiempo Fijo en Movimiento: Cuando la unidad está en movimiento,
     * éste evento se genera constantemente mientras este en circulación. Frecuencia
     * mínima del Reporte cada 5 minutos
     * @return void
     */
    public function Mov()
    {
        try {
            $this->json["username"] = $this->UserName;
            $this->json["event"] = 'Mov';
        

            $req = $this->SendCurlPetition(self::URLGetEvent, $this->json);
    
            return response()->json([
                'data' => $req,
                'json' => $this->json,
                'code' => 200
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'data' => 'error',
                'error' => $th->getMessage(),
                'code'  => 404
            ]);
        }
            
    }

    /**
     * Reporte de Tiempo Fijo sin Movimiento: Cuando la unidad está apagada y
     * detenida, éste evento se genera constantemente mientras esté detenida.
     * Frecuencia mínima del reporte cada 30 minutos
     * @return void
     */
    public function Detenido()
    {
        try {
            $this->json["username"] = $this->UserName;
            $this->json["event"] = 'Detenido';

            $req = $this->SendCurlPetition(self::URLGetEvent, $this->json);
    
            return response()->json([
                'data' => $req,
                'json' => $this->json,
                'code' => 200
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'data' => 'error',
                'error' => $th->getMessage(),
                'code'  => 404
            ]);
        }
    }

    /**
     * Ignición Encendida: Cuando la unidad se encienda
     * @return void
     */
    public function IgnEnc()
    {
        try{
            $this->json["username"] = $this->UserName;
            $this->json["event"] = 'IgnEnc';

            $req = $this->SendCurlPetition(self::URLGetEvent, $this->json);
    
            return response()->json([
                'data' => $req,
                'json' => $this->json,
                'code' => 200
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'data' => 'error',
                'error' => $th->getMessage(),
                'code'  => 404
            ]);
        }   
    }

    /**
     * Ignición Apagada: Cuando la unidad se apague
     * @return void
     */
    public function IgnApa()
    {
        try{
            $this->json["username"] = $this->UserName;
            $this->json["event"] = 'IgnApa';
        
            $req = $this->SendCurlPetition(self::URLGetEvent, $this->json);
    
            return response()->json([
                'data' => $req,
                'json' => $this->json,
                'code' => 200
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'data' => 'error',
                'error' => $th->getMessage(),
                'code'  => 404
            ]);
        }
    }

    /**
     * Detección Jammer: Diferenciar entre Jammer y baja/nula cobertura
     * @return void
     */
    public function DetJamm()
    {
        try {
            $this->json["username"] = $this->UserName;
            $this->json["event"] = 'DetJamm';
        
            $req = $this->SendCurlPetition(self::URLGetEvent, $this->json);
    
            return response()->json([
                'data' => $req,
                'json' => $this->json,
                'code' => 200
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'data' => 'error',
                'error' => $th->getMessage(),
                'code'  => 404
            ]);
        }
    }

    /**
     * Desconexión de Batería/Fuente de Poder: Cuando ha sido desconectada la
     * batería o fuente de poder de la unidad
     * @return void
     */
    public function DesconBat()
    {
        try{
            $this->json["username"] = $this->UserName;
            $this->json["event"] = 'DesconBat';
        
            $req = $this->SendCurlPetition(self::URLGetEvent, $this->json);
    
            return response()->json([
                'data' => $req,
                'json' => $this->json,
                'code' => 200
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'data' => 'error',
                'error' => $th->getMessage(),
                'code'  => 404
            ]);
        }
    }

    /**
     * Reconexión de Batería/Fuente de Poder: Cuando ha sido conectada la batería o
     * fuente de poder de la unidad
     * @return void
     */
    public function ReconBat()
    {
        try {
            $this->json["username"] = $this->UserName;
            $this->json["event"] = 'ReconBat';
        
            $req = $this->SendCurlPetition(self::URLGetEvent, $this->json);
    
            return response()->json([
                'data' => $req,
                'json' => $this->json,
                'code' => 200
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'data' => 'error',
                'error' => $th->getMessage(),
                'code'  => 404
            ]);
        }
    }


    /**
     * Funcion para la solicitud y creacion del token de autorizacion
     * Metodo: GET
     * Get all driver-vehicle assignments
     */
    public function CrateToken()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::URLGetToken,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'username: '.$this->UserName,
                'password: '.$this->Password
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $req = json_decode($response, true);
  
        if ($req['status_code'] === 200) { 
            Helper::envUpdate('BLACSOL_TOKEN', ' "' . $req['message'] . '" ', true);
            setcookie('token_blacsol_ws', json_encode($req['message']), time() + (60 * 10));
            $this->Token    = env('BLACSOL_TOKEN');
        }
    }


    /**
     * Funcion para la peticion de Eventos
     * Metodo : POST
     */
    public function SendCurlPetition($url, $data)
    {
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'token: '. $this->Token,
            ),
            CURLOPT_POSTFIELDS => json_encode($data),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $req = json_decode($response, true);
        return $req;
    }
}