<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');
    $router->group(['middleware' => ['auth.jwt']], function () use ($router) {
        $router->get('todo', 'TodoController@index');
        $router->get('todo/{todo_id}', 'TodoController@show');
        $router->post('todo', 'TodoController@store');
        $router->patch('todo/{todo_id}', 'TodoController@update');
        $router->delete('todo/{todo_id}', 'TodoController@destroy');
        $router->patch('todo/{todo_id}/status', 'TodoController@updateStatus');
    });
});
