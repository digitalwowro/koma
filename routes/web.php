<?php

Route::pattern('type', '[0-9]+');

//Route::get('/test', function () {
//    dd(App\Category::all()->each(function ($i) { $i->data = $i->data; })->toArray());
//});

Route::get ('/auth/login',           ['as' => 'login',           'uses' => 'Auth\LoginController@showLoginForm']);
Route::post('/auth/login',           ['as' => 'login.post',      'uses' => 'Auth\LoginController@login']);
Route::post('/auth/logout',          ['as' => 'logout',          'uses' => 'Auth\LoginController@logout']);
Route::get ('/auth/recover',         ['as' => 'recover',         'uses' => 'Auth\RecoverController@index']);
Route::post('/auth/recover',         ['as' => 'recover.post',    'uses' => 'Auth\RecoverController@post']);
Route::get ('/auth/recover/success', ['as' => 'recover.success', 'uses' => 'Auth\RecoverController@success']);
Route::get ('/auth/register',        ['as' => 'register',        'uses' => 'Auth\RegisterController@index']);
Route::post('/auth/register',        ['as' => 'register.post',   'uses' => 'Auth\RegisterController@post']);
Route::get ('/auth/register/success',['as' => 'register.success','uses' => 'Auth\RegisterController@success']);

Route::group(['middleware' => 'auth'], function() {
    Route::get ('/',        ['as' => 'home',           'uses' => 'DashboardController@index']);
    Route::get ('/profile', ['as' => 'profile',        'uses' => 'ProfileController@index']);
    Route::post('/profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);

    Route::resource('users',       'UserController');
    Route::resource('groups',      'GroupController');
    Route::resource('category',    'CategoryController');
    Route::resource('ip-category', 'IpCategoryController');
    Route::resource('ip-fields',   'IpFieldController');

    Route::post('/category/get-options', ['as' => 'category.get-options', 'uses' => 'CategoryController@getOptions']);

    Route::get   ('/item/{category}/create', ['as' => 'item.create',  'uses' => 'ItemController@create']);
    Route::get   ('/item/{category}',        ['as' => 'item.index',   'uses' => 'ItemController@index']);
    Route::get   ('/item/{id}/show',         ['as' => 'item.show',    'uses' => 'ItemController@show']);
    Route::post  ('/item/{category}',        ['as' => 'item.store',   'uses' => 'ItemController@store']);
    Route::get   ('/item/{id}/edit',         ['as' => 'item.edit',    'uses' => 'ItemController@edit']);
    Route::put   ('/item/{id}',              ['as' => 'item.update',  'uses' => 'ItemController@update']);
    Route::delete('/item/{id}',              ['as' => 'item.destroy', 'uses' => 'ItemController@destroy']);

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
