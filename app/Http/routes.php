<?php

Route::pattern('type', '[0-9]+');


Route::controller('auth', 'Auth\AuthController');

Route::group(['middleware' => 'auth'], function()
{
    Route::get('/', ['as' => 'home', 'uses' => 'DashboardController@index']);

    Route::resource('users',           'UserController');
    Route::resource('device-sections', 'DeviceSectionController');

    Route::post('/device-sections/get-options', ['as' => 'device-sections.get-options', 'uses' => 'DeviceSectionController@getOptions']);

    Route::get   ('/devices/{type}',           ['as' => 'devices.index',   'uses' => 'DeviceController@index']);
    Route::get   ('/devices/{type}/create',    ['as' => 'devices.create',  'uses' => 'DeviceController@create']);
    Route::post  ('/devices/{type}',           ['as' => 'devices.store',   'uses' => 'DeviceController@store']);
    Route::get   ('/devices/{type}/{id}/edit', ['as' => 'devices.edit',    'uses' => 'DeviceController@edit']);
    Route::put   ('/devices/{id}',             ['as' => 'devices.update',  'uses' => 'DeviceController@update']);
    Route::delete('/devices/{id}',             ['as' => 'devices.destroy', 'uses' => 'DeviceController@destroy']);
});
