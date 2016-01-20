<?php

Route::pattern('type', '[0-9]+');


Route::controller('auth', 'Auth\AuthController');

Route::group(['middleware' => 'auth'], function()
{
    Route::get('/', ['as' => 'home', 'uses' => 'DashboardController@index']);

    Route::resource('users',           'UserController');
    Route::resource('device-sections', 'DeviceSectionController');
    Route::resource('ip-categories',   'IpCategoryController');
    Route::resource('ip-fields',       'IpFieldController');

    Route::post('/device-sections/get-options', ['as' => 'device-sections.get-options', 'uses' => 'DeviceSectionController@getOptions']);

    Route::get   ('/devices/{type}/create',    ['as' => 'devices.create',  'uses' => 'DeviceController@create']);
    Route::get   ('/devices/{type}/{id}',      ['as' => 'devices.show',    'uses' => 'DeviceController@show']);
    Route::get   ('/devices/{type}',           ['as' => 'devices.index',   'uses' => 'DeviceController@index']);
    Route::post  ('/devices/{type}',           ['as' => 'devices.store',   'uses' => 'DeviceController@store']);
    Route::get   ('/devices/{type}/{id}/edit', ['as' => 'devices.edit',    'uses' => 'DeviceController@edit']);
    Route::put   ('/devices/{id}',             ['as' => 'devices.update',  'uses' => 'DeviceController@update']);
    Route::delete('/devices/{id}',             ['as' => 'devices.destroy', 'uses' => 'DeviceController@destroy']);

    Route::get   ('/ip/{category}/create',     ['as' => 'ip.create',  'uses' => 'IpController@create']);
    Route::get   ('/ip/{category}/{id}',       ['as' => 'ip.show',    'uses' => 'IpController@show']);
    Route::get   ('/ip/{category}',            ['as' => 'ip.index',   'uses' => 'IpController@index']);
    Route::post  ('/ip/{category}/{id}',       ['as' => 'ip.store',   'uses' => 'IpController@store']);
    Route::get   ('/ip/{category}/{id}/edit',  ['as' => 'ip.edit',    'uses' => 'IpController@edit']);
    Route::put   ('/ip/{id}',                  ['as' => 'ip.update',  'uses' => 'IpController@update']);
    Route::delete('/ip/{id}',                  ['as' => 'ip.destroy', 'uses' => 'IpController@destroy']);
});
