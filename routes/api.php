<?php

use Illuminate\Http\Request;
//use Illuminate\Routing\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//rutas protegidas
Route::group(['middleware'=>['jwt.auth'], 'prefix'=>'v1'],function(){
    
});
//rutas no protegidas
Route::group(['middleware' => [],'prefix'=>'v1'],function(){
    Route::post('/auth/login/{user}', 'TokensController@login');
    Route::post('/auth/refresh', 'TokensController@refreshToken');
    Route::get('/auth/expire', 'TokensController@logout');
});
