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



// Authentication routes...
Route::get('userInvitation/{tk}', 'UserInvitationController@index');
Route::post('userInvitation/register', 'UserInvitationController@register');
Route::get('auth/login/{login}/{password}', 'AuthController@login');
Route::get('auth/logout', 'AuthController@logout');
Route::group(['middleware' => 'auth'], function()
{
    Route::get('/', 'MainController@dashboard');
    Route::resource('titles', 'TitlesController');
	
	/*Titles Menegment*/
    //metadata
    Route::get('titles/metadata/{id}', 'TitleMenegment\MetadataController@metadataShow')->where('id', '[0-9]+');
    //media
    Route::get('titles/media/{filmId}', 'TitleMenegment\MediaController@mediaShow')->where('id', '[0-9]+');
    /*End Titles Menegment*/
	
    Route::get('account/users', [
        'middleware' => ['access:users', 'role:owner,administrator'],
        'uses' => 'Account\UsersController@listAll',
    ]);

    Route::post('account/users/create',[
		'middleware' => 'role:owner|administrator',
		"as" => 'account/users/create',
		"uses"=>'Account\UsersController@create'
	]);

    Route::get('account/features', [
        'middleware' => ['access:users', 'role:owner,administrator'],
        'uses' => 'Account\FeaturesController@features',
    ]);

    Route::post('account/users/update', [
        'as' => 'example',
        'middleware' => 'role:owner|administrator',
        'uses' => 'Account\UsersController@update',
    ]);
	
    Route::post('account/users/getTemplate', [
        'as' => 'account/users/getTemplate',
        'middleware' => 'role:owner|administrator',
        'uses' => 'Account\UsersController@getTemplate',
    ]);    
	
	Route::post('account/users/reSendInvitation', [
        'as' => 'account/users/reSendInvitation',
        'middleware' => 'role:owner|administrator',
        'uses' => 'Account\UsersController@reSendInvitation',
    ]);

    Route::post('account/users/destroy', [
        'as' => 'account/users/destroy',
        'middleware' => 'role:owner|administrator',
        'uses' => 'Account\UsersController@destroy',
    ]);	
	
    Route::post('account/users/invite', [
        'as' => 'example',
        'middleware' => 'role:owner|administrator',
        'uses' => 'Account\UsersController@invite',
    ]);
    Route::post('account/users/reInvite', [
        'as' => 'example',
        'middleware' => 'role:owner|administrator',
        'uses' => 'Account\UsersController@reInvite',
    ]);



    //Cinehost
    Route::get('features', [
        'middleware' => ['role:superadmin'],
        'uses' => 'FeaturesController@features',
    ]);	
});