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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
/////////
Route::middleware('jwtAuth')->group(function() {
    Route::get('logout','AuthController@logout');
    Route::get('me','AuthController@me');
    Route::get('payload','AuthController@payload');
    Route::resource('posts','postController');

/////////////////////////////
Route::resource('companies','CompanyController');
Route::resource('cards','CardController');
Route::post('localcards','CardController@localcards');
Route::post('nationalcards','CardController@nationalcards');
//////////used apies
Route::post('allcompanies','CompanyController@allcompanies');
Route::post('cardsbycompany','CardController@cardsbycompany');
Route::post('cardscount','CardController@cardscount');
Route::post('reserveorder','OrderController@reserveorder');
Route::post('finalorder','OrderController@finalorder');
Route::post('clientorder','OrderController@clientorder');
//////////////////////Sadad API 
Route::post('verify','SadadController@verify');
Route::post('confirm','SadadController@confirm');
//////////////////////////


});

Route::post('check_balance','CompanyController@check_balance');

Route::post('clientordser','OrderController@clientorder');

Route::post('login','AuthController@login');
//Route::middleware('jwt.auth')->post('login', 'API/AuthController@login');
