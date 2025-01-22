<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::prefix(env('user'))->namespace('User')->group(static function() {
    Route::middleware('auth')->group(static function () {


        /*
        |-----------------------------------------
        |Dashboard and Account Setting & Logout
        |-----------------------------------------
        */
        Route::get('/',[App\Http\Controllers\Admin\AdminController::class, 'index'])->name('dash');
        Route::get('dash',[App\Http\Controllers\Admin\AdminController::class, 'home'])->name('dash');
        Route::get('setting',[App\Http\Controllers\Admin\AdminController::class, 'setting'])->name('setting'); 
        Route::post('/setting',[App\Http\Controllers\Admin\AdminController::class, 'update']); 
        Route::get('account',[App\Http\Controllers\Admin\AdminController::class, 'account'])->name('account'); 
        Route::post('update_account',[App\Http\Controllers\Admin\AdminController::class, 'update_account']);
        Route::get('logout',[App\Http\Controllers\Admin\AdminController::class, 'logout'])->name('logoutAdmin');
        
        /*
        |-----------------------------------------
        |Gestor de SubCuentas de administracion
        |-----------------------------------------
        */ 
        Route::resource('subaccounts','\App\Http\Controllers\Admin\SubAccountController');
        Route::get('subaccounts',[App\Http\Controllers\Admin\SubAccountController::class, 'index'])->name('subaccounts');
        Route::get('subaccounts/delete/{id}',[App\Http\Controllers\Admin\SubAccountController::class, 'delete']);
        Route::get('subaccounts/status/{id}',[App\Http\Controllers\Admin\SubAccountController::class, 'status']);
        
        /*
        |-----------------------------------------
        |Cuentas Espejo
        |-----------------------------------------
        */ 
        Route::resource('mirror_beads','\App\Http\Controllers\Admin\MirrorBeadsController');
        Route::get('mirror_beads',[App\Http\Controllers\Admin\MirrorBeadsController::class, 'index'])->name('mirror_beads');
        Route::get('mirror_beads/delete/{id}',[App\Http\Controllers\Admin\MirrorBeadsController::class, 'delete']);
        Route::get('mirror_beads/status/{id}',[App\Http\Controllers\Admin\MirrorBeadsController::class, 'status']);
        
        /*
        |-----------------------------------------
        |Gestor de Rutas
        |-----------------------------------------
        */ 
        Route::resource('rutas','\App\Http\Controllers\Admin\RutasController');
        Route::get('rutas',[App\Http\Controllers\Admin\RutasController::class, 'index'])->name('rutas');
        Route::get('rutas/delete/{id}',[App\Http\Controllers\Admin\RutasController::class, 'delete']);
        Route::get('rutas/status/{id}',[App\Http\Controllers\Admin\RutasController::class, 'status']); 
        Route::get('rutas/getPoly/{origin}/{destin}',[App\Http\Controllers\Admin\RutasController::class, 'getPoly']);
        
        /*
        |-----------------------------------------
        |Gestor de Vehiculos
        |-----------------------------------------
        */ 
        Route::resource('vehicle_units','\App\Http\Controllers\Admin\VehicleUnitsController');
        Route::get('vehicle_units',[App\Http\Controllers\Admin\VehicleUnitsController::class, 'index'])->name('vehicle_units');
        Route::get('vehicle_units/delete/{id}',[App\Http\Controllers\Admin\VehicleUnitsController::class, 'delete']);
        Route::get('vehicle_units/status/{id}',[App\Http\Controllers\Admin\VehicleUnitsController::class, 'status']); 
        Route::post('vehicle_units/assign_box',[App\Http\Controllers\Admin\VehicleUnitsController::class, 'assign_box']);
        Route::post('vehicle_units/assign_gps',[App\Http\Controllers\Admin\VehicleUnitsController::class, 'assign_gps']);
        /*
        |-----------------------------------------
        |Gestor de Cajas
        |-----------------------------------------
        */ 
        Route::resource('truck_boxes','\App\Http\Controllers\Admin\TruckBoxesController');
        Route::get('truck_boxes',[App\Http\Controllers\Admin\TruckBoxesController::class, 'index'])->name('truck_boxes');
        Route::get('truck_boxes/delete/{id}',[App\Http\Controllers\Admin\TruckBoxesController::class, 'delete']);
        Route::get('truck_boxes/status/{id}',[App\Http\Controllers\Admin\TruckBoxesController::class, 'status']); 
        Route::post('truck_boxes/assign_gps',[App\Http\Controllers\Admin\TruckBoxesController::class, 'assign_gps']);

        
        /*
        |-----------------------------------------
        |Gestor de dispositivos
        |-----------------------------------------
        */ 
        Route::resource('dispositivos','\App\Http\Controllers\Admin\GpsDevicesController');
        Route::get('dispositivos',[App\Http\Controllers\Admin\GpsDevicesController::class, 'index'])->name('dispositivos');
        Route::get('dispositivos/delete/{id}',[App\Http\Controllers\Admin\GpsDevicesController::class, 'delete']);
        Route::get('dispositivos/status/{id}',[App\Http\Controllers\Admin\GpsDevicesController::class, 'status']); 


        /*
        |-----------------------------------------
        |Inbox de chats
        |-----------------------------------------
        */  
        Route::resource('chats_inbox','\App\Http\Controllers\Admin\ChatsInboxController');
        Route::get('chats_inbox',[App\Http\Controllers\Admin\ChatsInboxController::class, 'index'])->name('chats_inbox');
        Route::get('chats_inbox/view/{id}',[App\Http\Controllers\Admin\ChatsInboxController::class, 'view_inbox'])->name('view_inbox');
        Route::get('chats_inbox/delete/{id}',[App\Http\Controllers\Admin\ChatsInboxController::class, 'delete']);
        Route::get('chats_inbox/status/{id}',[App\Http\Controllers\Admin\ChatsInboxController::class, 'status']); 
        Route::post('chats_reply_inbox',[App\Http\Controllers\Admin\ChatsInboxController::class, 'chats_reply_inbox']); 
        /*
        |-----------------------------------------
        |Ajustes
        |-----------------------------------------
        */ 
        Route::get('ajustes',[App\Http\Controllers\Admin\AdminController::class, 'ajustes'])->name('ajustes');
        Route::post('update_ajustes',[App\Http\Controllers\Admin\AdminController::class, 'update_ajustes']);
     
    });

});
 


/*
|--------------------------------------------------------------------------
| Control de fallos
|--------------------------------------------------------------------------
*/
// Route::fallback(function () {
//     return view('errors.404');
// });
