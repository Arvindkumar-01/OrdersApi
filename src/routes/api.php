<?php
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

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Http\Controllers\Api'], function ($api) {
        $api->post('/orders', 'OrderController@store')->name('order.store');
        $api->patch('/orders/{id}', 'OrderController@update')->where('id', '[0-9]+')->name('order.update');
        $api->get('/orders', 'OrderController@list')->name('order.list');
    });
});
