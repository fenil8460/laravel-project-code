<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'credentials' => [
        'accountid' => env('BANDWIDTH_ACCOUNTID'),
        'appid' => env('BANDWIDTH_APPID'),
        'token' => env('BANDWIDTH_TOKEN'),
        'key' => env('BANDWIDTH_KEY'),
        'username' => env('BANDWIDTH_USERNAME'),
        'password' => env('BANDWIDTH_PASSWORD'),
        'dashboard' => env('BANDWIDTH_DASHBOARD'),
        'messageURL' => env('BANDWIDTH_MESSAGEURL')
    ],

];
