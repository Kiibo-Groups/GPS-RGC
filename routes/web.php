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


Route::group(['namespace' => 'App\Http\Controllers\Admin','prefix' => env('admin')], function(){

    

    
    Route::group(['middleware' => 'auth'], function(){

        /*
        |-----------------------------------------
        |Dashboard and Account Setting & Logout
        |-----------------------------------------
        */
        Route::get('/','AdminController@index'); 
        Route::get('home','AdminController@home');
        Route::get('setting','AdminController@setting');
        Route::post('setting','AdminController@update');
        Route::get('logout','AdminController@logout');

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
