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
    public function enviarEvento($evento): \Illuminate\Http\JsonResponse
    {
        // Validación del evento
        if (!is_array($evento) || empty($evento)) {
            return response()->json(['error' => 'Evento inválido.'], 400);
        }
        
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
