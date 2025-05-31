<?php
namespace App\Services;

use SoapClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class AVLService
{
    /**
     * QA
     * http://integraciones.qa.rcontrol.com.mx/Tracking/wcf/RCService.svc
     * ● Producción
     * http://gps.rcontrol.com.mx/Tracking/wcf/RCService.svc
     */

    private $wsdl = 'http://gps.rcontrol.com.mx/Tracking/wcf/RCService.svc?wsdl';
    private $usuario = 'user_avl_Salgo';
    private $password = 'iUOP$464psOc*2';

    public function getOrGenerateToken(): ?string
    {
        $token = trim(Cache::get('avl_token'));

        if (!$token || strlen($token) < 10) {
            try {
                $client = new SoapClient($this->wsdl, ['trace' => true, 'exceptions' => true]);

                $response = $client->__soapCall('GetUserToken', [
                    'parameters' => [
                        'userId' => $this->usuario,
                        'password' => $this->password,
                    ],
                ]);

                $token = $response->GetUserTokenResult->token ?? null;

                if ($token) {
                    Cache::put('avl_token', $token, now()->addHours(24));
                }
            } catch (Exception $e) {
                throw new Exception("Error al obtener token: " . $e->getMessage());
            }
        }

        return $token;
    }

    public function enviarEvento(array $evento): mixed
    {
        $token = $this->getOrGenerateToken();

        if (!$token) {
            throw new Exception("No se pudo obtener token válido.");
        }

        Log::info('Usando token AVL:', ['token' => $token]);
        
        try {
            $client = new SoapClient($this->wsdl, [
                'trace' => true,
                'exceptions' => true,
                'cache_wsdl' => WSDL_CACHE_NONE
            ]);

            $params = [
                'token' => $token,
                'events' => ['Event' => $evento]
            ];

            $response = $client->__soapCall('GPSAssetTracking', ['parameters' => $params]);

            if (isset($response->GPSAssetTrackingResult->AppointResult->idJob)) {
                return $response->GPSAssetTrackingResult->AppointResult->idJob;
            } else {
                throw new Exception("Error en la respuesta del servicio: " . json_encode($response));
            }
        } catch (Exception $e) {
            throw new Exception("Error al enviar evento: " . $e->getMessage());
        }
    }
}
