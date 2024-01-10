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
        |Gestor de Rutas
        |-----------------------------------------
        */ 
        Route::resource('rutas','\App\Http\Controllers\Admin\RutasController');
        Route::get('rutas',[App\Http\Controllers\Admin\RutasController::class, 'index'])->name('rutas');
        Route::get('rutas/delete/{id}',[App\Http\Controllers\Admin\RutasController::class, 'delete']);
        Route::get('rutas/status/{id}',[App\Http\Controllers\Admin\RutasController::class, 'status']); 
        
        /*
        |-----------------------------------------
        |Conexiones
        |-----------------------------------------
        */ 
        Route::get('conexiones',[App\Http\Controllers\Admin\AdminController::class, 'conexiones'])->name('conexiones');
     
    });

});


/*
|--------------------------------------------------------------------------
| Control de fallos
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return view('errors.404'); // template should exists
});
