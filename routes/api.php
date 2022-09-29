<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->group(['middleware' => ['throttle:60,1', 'bindings'], 'namespace' => 'App\Http\Controllers'], function ($api) {

        $api->get('ping', 'Api\PingController@index');

        $api->get('assets/{uuid}/render', 'Api\Assets\RenderFileController@show');

        $api->post('guest/fanRegister', 'Api\Users\UsersController@fanRegister');

        $api->post('guest/playerRegister', 'Api\Users\UsersController@playerRegister');

        $api->post('forgot_password', 'Api\Users\UsersController@forgotPassword');

        $api->post('reset_password', 'Api\Users\UsersController@resetPassword');

        $api->post('playerLogin', 'Api\Users\UsersController@playerLogin');

        $api->post('fanLogin', 'Api\Users\UsersController@fanLogin');
            
        // $api->group(['middleware' => ['role:Administrator']], function ($api) {

        $api->group(['middleware' => ['auth:api']], function ($api) {

            $api->group(['prefix' => 'users'], function ($api) {
                $api->get('/', 'Api\Users\UsersController@index');
                $api->post('/', 'Api\Users\UsersController@store');
                $api->get('/{uuid}', 'Api\Users\UsersController@show');
                $api->put('/{uuid}', 'Api\Users\UsersController@update');
                $api->patch('/{uuid}', 'Api\Users\UsersController@update');
                $api->delete('/{uuid}', 'Api\Users\UsersController@destroy');
            });

            $api->group(['prefix' => 'roles'], function ($api) {
                $api->get('/', 'Api\Users\RolesController@index');
                $api->post('/', 'Api\Users\RolesController@store');
                $api->get('/{uuid}', 'Api\Users\RolesController@show');
                $api->put('/{uuid}', 'Api\Users\RolesController@update');
                $api->patch('/{uuid}', 'Api\Users\RolesController@update');
                $api->delete('/{uuid}', 'Api\Users\RolesController@destroy');
            });

            $api->get('permissions', 'Api\Users\PermissionsController@index');

            $api->group(['prefix' => 'me'], function ($api) {
                $api->get('/', 'Api\Users\ProfileController@index');
                $api->put('/', 'Api\Users\ProfileController@update');
                $api->patch('/', 'Api\Users\ProfileController@update');
                $api->put('/password', 'Api\Users\ProfileController@updatePassword');
            });

            $api->get('useraddresses', 'Api\Users\UserAddressController@index');



            $api->group(['prefix' => 'me'], function ($api) {
                $api->get('/', 'Api\Users\UserAddressController@showUser');
                $api->get('/viewuseraddress', 'Api\Users\UserAddressController@show');
                $api->post('/createuseraddress', 'Api\Users\UserAddressController@store');
                $api->put('/updateuseraddress', 'Api\Users\UserAddressController@update');
                $api->patch('/updateuseraddress', 'Api\Users\UserAddressController@update');
                // $api->put('/password', 'Api\Users\UserAddressController@updatePassword');
                $api->delete('/deleteuseraddress', 'Api\Users\UserAddressController@destroy');
            });

            $api->post('/setWalletInterest', 'Api\v1\WalletInterestController@setWalletInterest');

            $api->get('/getWalletInterest', 'Api\v1\WalletInterestController@getWalletInterest');

            $api->group(['prefix' => 'assets'], function ($api) {
                $api->post('/', 'Api\Assets\UploadFileController@store');
                $api->delete('/{uuid}', 'Api\Assets\UploadFileController@destroy');
            });

            $api->get('/getCountries', 'Api\v1\Config\CountryStateCityController@index');

            $api->post('/getStates', 'Api\v1\Config\CountryStateCityController@getStates');

            $api->post('/getCities', 'Api\v1\Config\CountryStateCityController@getCities');
          
            $api->resource('/ConfStatus', 'Api\v1\Config\ConfStatusController');


            $api->resource('/ConfPaymentStatus', 'Api\v1\Config\ConfPaymentStatusController');

            $api->resource('/ConfPaymentMode', 'Api\v1\Config\ConfPaymentModeController');

            $api->resource('/News', 'Api\v1\NewsController');

            $api->resource('/Experience', 'Api\v1\ExperienceController');

            $api->resource('/MyWeek', 'Api\v1\MyWeekController');

            $api->resource('/Purchase', 'Api\v1\PurchaseController');

            $api->get('/playersList', 'Api\Users\UsersController@playersList');

            $api->get('/fansList', 'Api\Users\UsersController@fansList');

            // $api->post('stripeToken', 'Api\v1\StripeController@stripeToken')->name('stripe.token');
          


        });
   

   
    });

});
