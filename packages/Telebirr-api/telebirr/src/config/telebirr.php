<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telebirr API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration details for interacting with
    | the Telebirr API.
    |
    */

    'public_key' => env('TELEBIRR_PUBLIC_KEY'),
    'app_key' => env('TELEBIRR_APP_KEY'),
    'app_id' => env('TELEBIRR_APP_ID'),
    'api' => env('TELEBIRR_API_URL'),
    'short_code' => env('TELEBIRR_SHORT_CODE'),
    'notify_url' => env('TELEBIRR_NOTIFY_URL'),
    'return_url' => env('TELEBIRR_RETURN_URL'),
    'timeout_express' => env('TELEBIRR_TIMEOUT_EXPRESS'),
    'receive_name' => env('TELEBIRR_RECEIVE_NAME'),

];
