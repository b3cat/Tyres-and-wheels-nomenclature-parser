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


Route::get('/whitelist/make/{id}', 'WhitelistController@makeWhitelist');
Route::post('/whitelist/save', 'WhitelistController@saveWhitelist');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/nomenclature/test', 'NomenclatureController@testParser');
Route::post('/nomenclature/parse', 'NomenclatureController@parse');


Route::group(['middleware' => ['role:admin']], function () {
    Route::resource('users', 'Users\UserController');
    Route::resource('roles', 'Users\RoleController');
    Route::resource('permissions', 'Users\PermissionController');
    Route::delete('permissions', 'Users\PermissionController@actionsDestroy')->name('permissions.actions.destroy');
    Route::delete('roles', 'Users\RoleController@actionsDestroy')->name('roles.actions.destroy');
    Route::patch('users/{user}/roles/update', 'Users\UserController@rolesUpdate')->name('users.roles.update');
    Route::get('users/{user}/roles', 'Users\UserController@roles')->name('users.roles');
    Route::patch('roles/{role}/permissions/update', 'Users\RoleController@permissionsUpdate')->name('roles.permissions.update');
    Route::get('roles/{role}/permissions', 'Users\RoleController@permissions')->name('roles.permissions');
});

Route::group([
    'prefix' => 'tiresandwheels',
    'middleware' => ['role:content-manager']
], function () {
    Route::group([
        'prefix' => 'parser',
        'middleware' => ['role:admin']
    ], function () {
        //Ajax routes
        Route::post('regexps/add', 'NomenclatureController@addRegExp')
            ->name('parser.regexps.add');
        Route::post('regexps/delete/{id}', 'NomenclatureController@deleteRegExp')
            ->name('parser.regexps.delete');
        Route::post('regexp/update', 'NomenclatureController@updateRegExp')
            ->name('parser.regexps.update');
        Route::post('test', 'NomenclatureController@parserTest')
            ->name('parser.test');
        Route::post('pairfields/make', 'FieldController@makePairFields')
            ->name('parser.pairFields.make');

        //
        Route::get('regexps', 'NomenclatureController@showRegExps')->name('parser.regexps');
        Route::get('pairfields', 'FieldController@pairFields')->name('parser.pairFields');
    });
    //Ajax routes
    Route::get('whitelists', 'NomenclatureController@whitelists')->name('whitelists.index');
    Route::post('whitelists/search', 'NomenclatureController@whitelistsSearch')->name('whitelists.search');
    Route::get('whitelists/search/{id}', 'NomenclatureController@whitelistsGetModels')->name('whitelists.getmodels');
    Route::post('/whitelist/update', 'NomenclatureController@saveWhitelist')->name('whitelist.update');
    Route::get('whitelists/get/{id}', 'NomenclatureController@whitelistsGet')->name('whitelists.get');
    Route::get('/parsererror/{id}', 'NomenclatureController@parserError')->name('parser.error');
    Route::get('/parseagain/{id}', 'NomenclatureController@parseAgain')->name('parser.parseagain');
    Route::post('/products/update', 'ProductController@updateFromErrors')->name('taw.product.update');
    //
    Route::get('', 'NomenclatureController@index');
    Route::get('errors', 'NomenclatureController@errors');
    Route::get('nc-errors', 'NomenclatureController@nonCriticalErrors');
});
Route::group([
    'prefix' => 'apisettings',
    'middleware' => ['role:admin']
], function () {
    Route::get('', 'ApiController@index');
});
Route::get('/search/autocomplete', 'WhitelistController@autoCompleteSelect');
