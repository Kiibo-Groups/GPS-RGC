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

    Route::get('welcome','ApiController@welcome');

    Route::post('getToken','ApiController@getToken');
    Route::post('Webhook_rgc_api','ApiController@Webhook_rgc_api');
    
    Route::get('getDevice','ApiController@getDevice');
   
    Route::post('getGSMInfo','ApiController@getGSMInfo');
    /**
     * 
     * APIREST para el agregado de cronjobs
     * 
     */

    Route::get('PosiCont','ApiController@PosiCont');
    Route::get('Idle','ApiController@Idle');
    Route::get('Mov','ApiController@Mov');
    Route::get('Detenido','ApiController@Detenido');
    Route::get('ChkIgn','ApiController@ChkIgn');
    Route::get('IgnEnc','ApiController@IgnEnc');
    Route::get('IgnApa','ApiController@IgnApa');
    Route::get('DetJamm','ApiController@DetJamm');
    Route::get('DesconBat','ApiController@DesconBat');
    Route::get('ReconBat','ApiController@ReconBat');
     
    
});
