<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

use App\Http\Controllers\AVLController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//  return $request->user();

Route::group(array('namespace' => 'App\Http\Controllers\Api'), function () {

    Route::get('welcome',[ApiController::class, 'welcome']);
    Route::post('getToken',[ApiController::class, 'getToken']);
    Route::get('setPusher',[ApiController::class, 'setPusher']);
    /**
     * Webhook para recibir informacion
     */
    Route::post('Webhook_rgc_api',[ApiController::class, 'Webhook_rgc_api']);
    Route::post('webhook_rgc_csv',[ApiController::class, 'webhook_rgc_csv']);
    
    /**
     * Guardado del Paquete del GPS fisico
     */
    Route::post('getGSMInfo',[ApiController::class, 'getGSMInfo']);
    
    /**
     * Obtiene coordenadas GPS en tiempo real de cualquier tipo de paquete
     */
    Route::post('getRealTimeCoordinates',[ApiController::class, 'getRealTimeCoordinates']);
    
    /**
     * Obtiene coordenadas como string simple (lat,lon)
     */
    Route::post('getCoordinatesString',[ApiController::class, 'getCoordinatesString']);
            
    /**
     * Obtenemos todos los dispositivos en la BD
     */
    Route::get('getAllDispositives',[ApiController::class, 'getAllDispositives'])->name('getAllDispositives');

    /**
     * 
     * APIREST para el agregado de cronjobs
     * 
     */
    Route::get('PosiCont',[ApiController::class, 'PosiCont']);
    Route::get('Idle', [ApiController::class, 'Idle']);
    Route::get('Mov', [ApiController::class, 'Mov']);
    Route::get('Detenido', [ApiController::class, 'Detenido']);
    Route::get('ChkIgn', [ApiController::class, 'ChkIgn']);
    Route::get('IgnEnc', [ApiController::class, 'IgnEnc']);
    Route::get('IgnApa', [ApiController::class, 'IgnApa']);
    Route::get('DetJamm', [ApiController::class, 'DetJamm']);
    Route::get('DesconBat', [ApiController::class, 'DesconBat']);
    Route::get('ReconBat', [ApiController::class, 'ReconBat']);
    // API Para CronJob de puslasiones
    Route::post('/avl/cronjob-avl', [ApiController::class, 'SetPulseAVL']);
    

    Route::post('/avl/token', [AVLController::class, 'obtenerToken']);
    Route::post('/avl/enviar-evento', [AVLController::class, 'enviarEvento']);


    Route::get('/decode-packet', [ApiController::class, 'DecodePacket']);
    
});
