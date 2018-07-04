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
Route::get('/showresults', 'TestController@showTestResults');

Route::get('/test', 'NomenclatureController@index');

Route::get('/whitelist/make/{id}', 'WhitelistController@makeWhitelist');
Route::post('/whitelist/save', 'WhitelistController@saveWhitelist');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/nomenclature/test', 'NomenclatureController@testParser');
Route::post('/nomenclature/parse', 'NomenclatureController@parse');


Route::group(['middleware' => ['role:admin']], function () {
    Route::resource('users', 'Users\UserController');
    //    Route::resource('posts', 'PostController');
    Route::resource('roles', 'Users\RoleController');
    Route::resource('permissions', 'Users\PermissionController');

    Route::delete('permissions', 'Users\PermissionController@actionsDestroy')->name('permissions.actions.destroy');
    Route::delete('roles', 'Users\RoleController@actionsDestroy')->name('roles.actions.destroy');

    //    Route::group(['middleware' => ['permission:users__roles--update']], function () {
    Route::patch('users/{user}/roles/update', 'Users\UserController@rolesUpdate')->name('users.roles.update');
    Route::get('users/{user}/roles', 'Users\UserController@roles')->name('users.roles');
    //    });
    //    Route::group(['middleware' => ['permission:roles__permissions--update']], function () {
    Route::patch('roles/{role}/permissions/update', 'Users\RoleController@permissionsUpdate')->name('roles.permissions.update');
    Route::get('roles/{role}/permissions', 'Users\RoleController@permissions')->name('roles.permissions');
    //    });
});