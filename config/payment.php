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
            'weekFreeAmount' => 0,
            'weekFreeLimit' => 0,
        ],
        'private' => [
            'commission' => 0.3,
            'weekFreeAmount' => 1000,
            'weekFreeLimit' => 3,
        ],
    ],
    


];
