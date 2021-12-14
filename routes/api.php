<?php

use Illuminate\Http\Request;

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
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});

/*Route::group(['prefix' => 'cameras', 'middleware' => 'auth:api'], function () {
    Route::get('/', 'CameraController@index');
    Route::get('{id}/stream', ['as' => 'cameras.stream', 'uses' => 'CameraController@stream']);
});*/
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

Route::get('cameras/{id}/archive/{file}', ['as' => 'cameras.archive', 'uses' => 'CameraController@archivefile']);

Route::middleware('auth:api')->group(function () {
    Route::get('cameras', 'CameraController@index');
    Route::get('cameras/{id}', 'CameraController@show');
    Route::get('cameras/{id}/snapshot', 'CameraController@snapshot');
    Route::get('cameras/{id}/stream/{quality}/{file}', ['as' => 'cameras.stream', 'uses' => 'CameraController@stream']);
    Route::get('cameras/{id}/archive', ['as' => 'cameras.archive', 'uses' => 'CameraController@archive']);
    //Route::post('cameras', ['as' => 'users.store', 'uses' => 'UserController@store', 'middleware' => ['permission:users-create']]);
    Route::put('cameras/{id}', ['as' => 'cameras.update', 'uses' => 'CameraController@update']);//, 'middleware' => ['permission:users-edit']]);
    Route::delete('cameras/{id}', ['as' => 'cameras.destroy', 'uses' => 'CameraController@destroy']);//, 'middleware' => ['permission:users-delete']]);
    
    Route::get('users', ['as' => 'users.index', 'uses' => 'UserController@index']);//, 'middleware' => ['permission:users-list']]);
    Route::get('users/{user}', ['as' => 'users.show', 'uses' => 'UserController@show']);//, 'middleware' => ['permission:users-list']]);
    Route::post('users', ['as' => 'users.store', 'uses' => 'UserController@store']);//, 'middleware' => ['permission:users-create']]);
    Route::put('users/{user}', ['as' => 'users.update', 'uses' => 'UserController@update']);//, 'middleware' => ['permission:users-edit']]);
    Route::delete('users/{user}', ['as' => 'users.destroy', 'uses' => 'UserController@destroy']);//, 'middleware' => ['permission:users-delete']]);

    Route::get('permissions', ['uses' => 'PermissionsController@index']);

    Route::get('roles', ['uses' => 'RoleController@index']);
    Route::get('roles/{id}', ['uses' => 'RoleController@show']);//, 'middleware' => ['permission:users-list']]);
    Route::post('roles', ['uses' => 'RoleController@store']);//, 'middleware' => ['permission:users-create']]);
    Route::put('roles/{id}', ['uses' => 'RoleController@update']);//, 'middleware' => ['permission:users-edit']]);
    Route::delete('roles/{id}', ['uses' => 'RoleController@destroy']);//, 'middleware' => ['permission:users-delete']]);

    Route::get('devices', ['as' => 'devices.index', 'uses' => 'DeviceController@index']);//, 'middleware' => ['permission:devices-list']]);
    Route::get('devices/{device}', ['as' => 'devices.show', 'uses' => 'DeviceController@show']);//, 'middleware' => ['permission:devices-list']]);
    Route::post('devices', ['as' => 'devices.store', 'uses' => 'DeviceController@store']);//, 'middleware' => ['permission:devices-create']]);
    Route::put('devices/{device}', ['as' => 'devices.update', 'uses' => 'DeviceController@update']);//, 'middleware' => ['permission:devices-edit']]);
    Route::delete('devices/{device}', ['as' => 'devices.destroy', 'uses' => 'DeviceController@destroy']);//, 'middleware' => ['permission:devices-delete']]);
    
    Route::get('devices/{device}/data', ['uses' => 'DeviceParamController@getData']);//, 'middleware' => ['permission:devices-list']]);
    Route::post('devices/{id}/notifications', ['uses' => 'DeviceController@editNotifications', 'middleware' => ['permission:devices-edit']]);
    Route::post('devices/{id}/command', ['uses' => 'DeviceController@sendCommand', 'middleware' => ['permission:devices-edit']]);

    Route::post('devices/{id}/scatch', ['uses' => 'DeviceController@uploadScatch', 'middleware' => ['permission:devices-edit']]);
    Route::put('params/{id}', ['uses' => 'DeviceParamController@update']);//, 'middleware' => ['permission:devices-edit']]);

    Route::get('units', ['as' => 'units.index', 'uses' => 'UnitController@index', 'middleware' => ['permission:directory-list']]);
    Route::get('units/{unit}', ['as' => 'units.show', 'uses' => 'UnitController@show', 'middleware' => ['permission:directory-list']]);
    Route::post('units', ['as' => 'units.store', 'uses' => 'UnitController@store', 'middleware' => ['permission:directory-edit']]);
    Route::put('units/{unit}', ['as' => 'units.update', 'uses' => 'UnitController@update', 'middleware' => ['permission:directory-edit']]);
    Route::delete('units/{unit}', ['as' => 'units.destroy', 'uses' => 'UnitController@destroy', 'middleware' => ['permission:directory-edit']]);

    Route::get('devicesgroups', ['as' => 'devicesgroups.index', 'uses' => 'DevicesGroupController@index']);//, 'middleware' => ['permission:directory-list']]);
    Route::get('devicesgroups/{devicesgroup}', ['as' => 'devicesgroups.show', 'uses' => 'DevicesGroupController@show']);//, 'middleware' => ['permission:directory-list']]);
    Route::post('devicesgroups', ['as' => 'devicesgroups.store', 'uses' => 'DevicesGroupController@store']);//, 'middleware' => ['permission:directory-edit']]);
    Route::put('devicesgroups/{devicesgroup}', ['as' => 'devicesgroups.update', 'uses' => 'DevicesGroupController@update']);//, 'middleware' => ['permission:directory-edit']]);
    Route::delete('devicesgroups/{devicesgroup}', ['as' => 'devicesgroups.destroy', 'uses' => 'DevicesGroupController@destroy']);//, 'middleware' => ['permission:directory-edit']]);

    Route::get('camerasgroups', ['uses' => 'CamerasGroupController@index']);//, 'middleware' => ['permission:directory-list']]);
    Route::get('camerasgroups/{id}', ['uses' => 'CamerasGroupController@show']);//, 'middleware' => ['permission:directory-list']]);
    Route::post('camerasgroups', ['uses' => 'CamerasGroupController@store']);//, 'middleware' => ['permission:directory-edit']]);
    Route::put('camerasgroups/{id}', ['uses' => 'CamerasGroupController@update']);//, 'middleware' => ['permission:directory-edit']]);
    Route::delete('camerasgroups/{id}', ['uses' => 'CamerasGroupController@destroy']);//, 'middleware' => ['permission:directory-edit']]);


    /*Route::get('cameras', ['as' => 'cameras.index', 'uses' => 'CameraController@index']);//, 'middleware' => ['permission:cameras-list']]);
    Route::get('cameras/{id}', ['as' => 'cameras.show', 'uses' => 'CameraController@show', 'middleware' => ['permission:cameras-list']]);
    Route::get('cameras/{id}/stream/{filename}', ['as' => 'cameras.stream', 'uses' => 'CameraController@stream', 'middleware' => ['permission:cameras-list']]);
    Route::post('cameras', ['as' => 'cameras.store', 'uses' => 'CameraController@store', 'middleware' => ['permission:cameras-edit']]);
    Route::put('cameras/{id}', ['as' => 'cameras.update', 'uses' => 'CameraController@update', 'middleware' => ['permission:cameras-edit']]);
    Route::delete('cameras/{id}', ['as' => 'cameras.destroy', 'uses' => 'CameraController@destroy', 'middleware' => ['permission:cameras-edit']]);
*/

});

//Route::get('cameras/{id}/stream/{filename}', ['as' => 'cameras.stream', 'uses' => 'CameraController@stream' ]);//, 'middleware' => ['permission:cameras-list']]);

Route::post('devices/collector/add', 'CollectorController@add');
Route::get('devices/{key}/scatch', 'CollectorController@updateOTA');
