<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix'=>'v1'], function() use ($router){
	$router->group(['prefix'=>'auth'], function() use ($router){
		$router->post('login', "AuthController@login");
		$router->post('register', "AuthController@register");
		$router->post('check_email', "AuthController@check_email");
		$router->get('test_sms', "AuthController@testing_sms");
	});

	$router->group(['middleware'=>'auth'], function() use ($router){
		$router->group(['prefix'=>'auth'], function() use ($router){
			$router->get('email_verify', "AuthController@email_verify");
			$router->get('check_store', "AuthController@check_store");
			$router->post('send_otp', "AuthController@send_otp");
			$router->post('check_mobile', "AuthController@check_mobile");
			$router->post('mobile_otp', "AuthController@send_mobile_otp");
			$router->post('create_store', "AuthController@create_store");
		});

		// Product Controller
		$router->get('product', "v1\ProductController@index");
		$router->post('product', "v1\ProductController@create");
		$router->put('product/{id}', "v1\ProductController@update");
		$router->delete('product/{id}', "v1\ProductController@delete");
		
		// Category Controller
		$router->get('category[/{id}]', "v1\StoreCategoryController@index");
		$router->post('category', "v1\StoreCategoryController@create");
		$router->put('category/{id}', "v1\StoreCategoryController@update");
		$router->delete('category/{id}', "v1\StoreCategoryController@delete");

		// Orders
		$router->get('orders', "v1\OrderController@index");

		// Customer
		$router->get('customers', "v1\CustomerController@index");
	});
});