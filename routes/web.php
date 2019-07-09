<?php

Route::pattern('type', '[0-9]+');

Route::get ('/auth/login',  ['as' => 'login',      'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('/auth/login',  ['as' => 'login.post', 'uses' => 'Auth\LoginController@login']);
Route::post('/auth/logout', ['as' => 'logout',     'uses' => 'Auth\LoginController@logout']);

Route::group(['middleware' => 'auth'], function() {
    Route::get ('/',        ['as' => 'home',           'uses' => 'DashboardController@index']);
    Route::get ('/profile', ['as' => 'profile',        'uses' => 'ProfileController@index']);
    Route::post('/profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);

    Route::resource('users',          'UserController');
    Route::resource('device-section', 'DeviceSectionController');
    Route::resource('ip-category',    'IpCategoryController');
    Route::resource('ip-fields',      'IpFieldController');

    Route::post('/device-section/get-options', ['as' => 'device-section.get-options', 'uses' => 'DeviceSectionController@getOptions']);

    Route::get   ('/device/{type}/create',      ['as' => 'device.create',  'uses' => 'DeviceController@create']);
    Route::get   ('/device/{type}/{category?}', ['as' => 'device.index',   'uses' => 'DeviceController@index'])->where('category', '\w{8}');
    Route::get   ('/device/{id}/show',          ['as' => 'device.show',    'uses' => 'DeviceController@show']);
    Route::post  ('/device/{type}',             ['as' => 'device.store',   'uses' => 'DeviceController@store']);
    Route::get   ('/device/{id}/edit',          ['as' => 'device.edit',    'uses' => 'DeviceController@edit']);
    Route::put   ('/device/{id}',               ['as' => 'device.update',  'uses' => 'DeviceController@update']);
    Route::delete('/device/{id}',               ['as' => 'device.destroy', 'uses' => 'DeviceController@destroy']);

    Route::get   ('/ip/subnet/{subnet}/list', ['as' => 'ip.subnet-list', 'uses' => 'IpController@subnetList']);
    Route::get   ('/ip/subnet/{subnet}',      ['as' => 'ip.subnet',      'uses' => 'IpController@subnet']);
    Route::get   ('/ip/{category}/create',    ['as' => 'ip.create',      'uses' => 'IpController@create']);
    Route::get   ('/ip/{id}/show',            ['as' => 'ip.show',        'uses' => 'IpController@show']);
    Route::get   ('/ip/{category}',           ['as' => 'ip.index',       'uses' => 'IpController@index']);
    Route::post  ('/ip/{category}',           ['as' => 'ip.store',       'uses' => 'IpController@store']);
    Route::get   ('/ip/{category}/{id}/edit', ['as' => 'ip.edit',        'uses' => 'IpController@edit']);
    Route::put   ('/ip/{id}',                 ['as' => 'ip.update',      'uses' => 'IpController@update']);
    Route::delete('/ip/{id}',                 ['as' => 'ip.destroy',     'uses' => 'IpController@destroy']);

    Route::post('/device-section/{id}/share', ['as' => 'device-section.share', 'uses' => 'DeviceSectionController@share']);
    Route::post('/device/{id}/share',         ['as' => 'device.share',         'uses' => 'DeviceController@share']);
    Route::post('/ip-category/{id}/share',    ['as' => 'ip-category.share',    'uses' => 'IpCategoryController@share']);
    Route::post('/ip/{id}/share',             ['as' => 'ip.share',             'uses' => 'IpController@share']);

    Route::post('/share/with', ['as' => 'share.with', 'uses' => 'ShareController@with']);
    Route::post('/share/post', ['as' => 'share.post', 'uses' => 'ShareController@post']);
});
