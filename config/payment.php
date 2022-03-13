<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Commission Options
    |--------------------------------------------------------------------------
    |
    | The following configurations are used for calculating each payment
    | commission.
    | If you changed any of these settings please keep in mind to run:
    | php artisan optimmize
    |
    */

    'deposit' => [
        'business' => [
            'commission' => 0.03,
        ],
        'private' => [
            'commission' => 0.03,
        ],
    ],

    'withdraw' => [
        'business' => [
            'commission' => 0.5,
        ],
        'private' => [
            'commission' => 0.3,
            'commissionFreeAmount' => 1000,
            'commissionFreeLimit' => 3,
        ],
    ],
    
    'currencyRateSource' => 'https://developers.paysera.com/tasks/api/currency-exchange-rates',

];
