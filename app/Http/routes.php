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
    Route::get('titles', 'TitlesController@titlesShow');

	/*Titles Menegment*/
    Route::group(['middleware' => 'filmPermission'], function(){
         Route::get('titles/metadata/{filmID}', 'TitleManagement\MetadataController@metadataShow')->where('filmID', '[0-9]+');
         Route::get('titles/media/{filmID}', 'TitleManagement\MediaController@mediaShow')->where('filmID', '[0-9]+');
         Route::get('titles/rights/{filmID}', 'TitleManagement\RightsController@rightsShow')->where('filmID', '[0-9]+');
         /*Route::get('titles/rights/{filmID}', [
             'middleware' => 'rightsPermission',
             'uses' => 'TitleManagement\RightsController@rightsShow',
         ])->where('filmID', '[0-9]+');*/
    });
    /*End Titles Menegment*/

    /*Xchange routing*/
    Route::get('xchange/contentProvider', 'Xchange\ProfileController@profileShow');
    Route::get('CPTitles', 'Xchange\CPTitlesController@CPTitlesShow');
    Route::get('xchange/titles', 'Xchange\XchangeTitlesController@xchangeTitlesShow');
    Route::get('xchange/stores', 'Xchange\XchangeStoresController@xchangeStoresShow');
    Route::get('xchange/contentProviders', 'Xchange\XchangeContentProvidersController@contentProvidersShow');
    /*End Xchange routing*/

    Route::get('partner/stores', 'PartnerStores\PartnerStoresController@partnerStoresShow');
	
	
    Route::get('store/profile', 'Store\ProfileController@profileShow');
    Route::get('store/settings', 'Store\SettingsController@settingsShow');
    Route::get('store/userManagement', 'Store\UserManagementController@usersShow');
    Route::get('store/contentProviders', 'Store\ContentProvidersController@contentProvidersShow');
    Route::get('store/slider', 'Store\SliderController@sliderShow');
    Route::get('store/channelsManager', 'Store\ChannelsManagerController@channelsManagerShow');
    Route::get('store/subscriptions', 'Store\SubscriptionsController@subscriptionsShow');
    Route::get('store/giftVoucher', 'Store\GiftVoucherController@giftVoucherShow');


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