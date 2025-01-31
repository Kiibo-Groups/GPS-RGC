<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

    Route::get('welcome',[App\Http\Controllers\api\ApiController::class, 'welcome']);
    Route::post('getToken',[App\Http\Controllers\api\ApiController::class, 'getToken']);

    /**
     * Webhook para recibir informacion
     */
    Route::post('Webhook_rgc_api',[App\Http\Controllers\api\ApiController::class, 'Webhook_rgc_api']);
     
    /**
     * Guardado del Paquete del GPS fisico
     */
    Route::post('getGSMInfo',[App\Http\Controllers\api\ApiController::class, 'getGSMInfo']);

    /**
     * Obtenemos todos los dispositivos en la BD
     */
    Route::get('getAllDispositives',[App\Http\Controllers\api\ApiController::class, 'getAllDispositives'])->name('getAllDispositives');

    /**
     * 
     * APIREST para el agregado de cronjobs
     * 
     */
    Route::get('PosiCont',[App\Http\Controllers\api\ApiController::class, 'PosiCont']);
    Route::get('Idle', [App\Http\Controllers\api\ApiController::class, 'Idle']);
    Route::get('Mov', [App\Http\Controllers\api\ApiController::class, 'Mov']);
    Route::get('Detenido', [App\Http\Controllers\api\ApiController::class, 'Detenido']);
    Route::get('ChkIgn', [App\Http\Controllers\api\ApiController::class, 'ChkIgn']);
    Route::get('IgnEnc', [App\Http\Controllers\api\ApiController::class, 'IgnEnc']);
    Route::get('IgnApa', [App\Http\Controllers\api\ApiController::class, 'IgnApa']);
    Route::get('DetJamm', [App\Http\Controllers\api\ApiController::class, 'DetJamm']);
    Route::get('DesconBat', [App\Http\Controllers\api\ApiController::class, 'DesconBat']);
    Route::get('ReconBat', [App\Http\Controllers\api\ApiController::class, 'ReconBat']);
    
});
