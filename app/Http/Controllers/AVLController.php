<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\AVLService;
use SoapClient;
use Exception;

class AVLController extends Controller
{
    protected AVLService $avlService;

    public function __construct(AVLService $avlService)
    {
        $this->avlService = $avlService;
    }

    /**
     * Envía un evento al servicio AVL utilizando el token almacenado.
     * El evento incluye información del vehículo y sus condiciones actuales.
     */
    public function enviarEvento()
    {
        
        $evento = [
            'altitude' => 0,
            'asset' => 'SALGO1234',
            'battery' => 100,
            'code' => '1',
            'course' => 0,
            'customer' => [
                'id' => '0',
                'name' => 'SALGO FREIGHT LOGISTICS'
            ],
            'date' => now()->format('Y-m-d\TH:i:s'),
            'direction' => 'Norte',
            'humidity' => 75.5,
            'ignition' => true,
            'latitude' => 25.59145,
            'longitude' => -100.24647,
            'odometer' => 1000,
            'serialNumber' => 'SN123456789',
            'shipment' => '0',
            'speed' => 80,
            'temperature' => 32.5,
            'vehicleType' => 'Tracto',
            'vehicleBrand' => 'Kenworth',
            'vehicleModel' => 'T680',
        ];

        try {
            $idJob = $this->avlService->enviarEvento($evento);
            return response()->json(['idJob' => $idJob]);
        } catch (Exception $e) {
            // Decodifica error SOAP si es posible
            if (str_contains($e->getMessage(), 'Autentificación incorrecta')) {
                Cache::forget('avl_token'); // limpia token si es inválido
            }

            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
