<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {
    var_dump(Auth::user());
    echo "<pre>";
    var_dump(Session::all());
    echo "</pre>";
    return view('welcome');
});*/

Route::get('/', 'TitlesController@listAll');

// Authentication routes...
Route::get('auth/login/{login}/{password}', 'AuthController@login');
Route::get('auth/logout', 'AuthController@logout');



Route::resource('titles', 'TitlesController');