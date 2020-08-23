<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Account
    |--------------------------------------------------------------------------
    |
    | This is the default account to be used when none is specified.
    */

    'default' => env('MPESA_DEFAULT'),

    /*
    |--------------------------------------------------------------------------
    | Native File Cache Location
    |--------------------------------------------------------------------------
    |
    | When using the Native Cache driver, this will be the relative directory
    | where the cache information will be stored.
    */

    'cache_location' => '../cache',

    /*
    |--------------------------------------------------------------------------
    | Accounts
    |--------------------------------------------------------------------------
    |
    | These are the accounts that can be used with the package. You can configure
    | as many as needed. Two have been setup for you.
    |
    | Sandbox: Determines whether to use the sandbox, Possible values: sandbox | production
    | Initiator: This is the username used to authenticate the transaction request
    | LNMO:
    |    paybill: Your paybill number
    |    shortcode: Your business shortcode
    |    passkey: The passkey for the paybill number
    |    callback: Endpoint that will be be queried on completion or failure of the transaction.
    |
    */

    'accounts' => [

        'staging' => [
            'sandbox' => true,
            'key' => env('MPESA_KEY'),
            'secret' => env('MPESA_SECRET'),
            'initiator' => env('MPESA_INITIATOR'),
            'id_validation_callback' => env('MPESA_VALIDATION'),
            'lnmo' => [
                'paybill' => env('MPESA_PAYBILL'),
                'shortcode' => env('MPESA_SHORTCODE'),
                'passkey' => env('MPESA_PASSKEY'),
                'callback' => env('MPESA_CALLBACK'),
            ]
        ],

        'production' => [
            'sandbox' => false,
            'key' => env('MPESA_KEY'),
            'secret' => env('MPESA_SECRET'),
            'initiator' => env('MPESA_INITIATOR'),
            'id_validation_callback' => env('MPESA_VALIDATION'),
            'lnmo' => [
                'paybill' => env('MPESA_PAYBILL'),
                'shortcode' => env('MPESA_SHORTCODE'),
                'passkey' => env('MPESA_PASSKEY'),
                'callback' => env('MPESA_CALLBACK'),
            ]
        ],
    ],
];
