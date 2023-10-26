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


// Route::group(['namespace' => 'App\Http\Controllers\Admin','prefix' => env('admin')], function(){
//     Route::group(['middleware' => 'auth'], function(){
    
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
