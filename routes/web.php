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
    Route::resource('groups',         'GroupController');
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

    Route::get   ('/subnet/subnet/{subnet}/list', ['as' => 'subnet.subnet-list', 'uses' => 'SubnetController@subnetList']);
    Route::get   ('/subnet/subnet/{subnet}',      ['as' => 'subnet.subnet',      'uses' => 'SubnetController@subnet']);
    Route::get   ('/subnet/{category}/create',    ['as' => 'subnet.create',      'uses' => 'SubnetController@create']);
    Route::get   ('/subnet/{id}/show',            ['as' => 'subnet.show',        'uses' => 'SubnetController@show']);
    Route::get   ('/subnet/{category}',           ['as' => 'subnet.index',       'uses' => 'SubnetController@index']);
    Route::post  ('/subnet/{category}',           ['as' => 'subnet.store',       'uses' => 'SubnetController@store']);
    Route::get   ('/subnet/{category}/{id}/edit', ['as' => 'subnet.edit',        'uses' => 'SubnetController@edit']);
    Route::put   ('/subnet/{id}',                 ['as' => 'subnet.update',      'uses' => 'SubnetController@update']);
    Route::delete('/subnet/{id}',                 ['as' => 'subnet.destroy',     'uses' => 'SubnetController@destroy']);
    Route::post  ('/subnet/{id}/unassign',        ['as' => 'subnet.unassign',    'uses' => 'SubnetController@unassign']);

    Route::post('/share/with', ['as' => 'share.with', 'uses' => 'ShareController@with']);
    Route::post('/share/post', ['as' => 'share.post', 'uses' => 'ShareController@post']);
});
