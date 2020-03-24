<?php

return [

    'paypal' => [
        'mode'      => env('PAYPAL_MODE', 'live'),
        'username'  => env('PAYPAL_USERNAME', 'prof.alolayan_api1.gmail.com'),
        'password'  => env('PAYPAL_PASSWORD', 'H3QTY22Y2T5VPBVK'),
        'signature' => env('PAYPAL_SIGNATURE', 'AteqDFq8qSmENryr7KX0vAzjxGc3AgNuWYZ6BDD.gQ3d94QmytRJy5f8'),
    ],

];



// return [
//     'paypal' => [
//         'mode'      => env('PAYPAL_MODE', 'sandbox'),
//         'username'  => env('PAYPAL_USERNAME', 'vipulbusiness1_api1.gmail.com'),
//         'password'  => env('PAYPAL_PASSWORD', 'T8NZZVAS5VFSZ2RR'),
//         'signature' => env('PAYPAL_SIGNATURE', 'AKwUpvimaCAfmfJ-.PhckWeGqTcrAredKyzofJaBPuB1s3WC7VAvP0qz'),
//     ],

// ];
