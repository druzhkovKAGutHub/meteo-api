<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

//Broadcast::routes(['middleware' => ['client_credentials']]);

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(["prefix" => "api", "middleware" => "auth:api"]);
Broadcast::channel('device.{id}', function () {
    return true;
});
Broadcast::channel('device-config.{key}', function () {
    return true;
});
Broadcast::channel('device-param-data', function () {
    return true;
});


