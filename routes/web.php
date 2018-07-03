<?php

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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/manufacturers', 'TestController@testManufacturers');
Route::get('/manufacturers/{id}/models', 'TestController@testModels');

Route::get('/test', 'NomenclatureController@index');

Route::get('/whitelist/make/{id}', 'WhitelistController@makeWhitelist');
Route::post('/whitelist/save', 'WhitelistController@saveWhitelist');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/nomenclature/test', 'NomenclatureController@testParser');
Route::post('/nomenclature/parse', 'NomenclatureController@parse');
