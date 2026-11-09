<?php

return [
    'email' => env('AGENCE_EMAIL', 'contact@maamkhalifa.sn'),
    'telephone' => env('AGENCE_TELEPHONE', '+221 77 000 00 00'),

    /*
    | Numéros marchands sur lesquels les clients règlent les prestations.
    */
    'paiement' => [
        'wave' => env('AGENCE_WAVE', '+221 77 000 00 00'),
        'orange_money' => env('AGENCE_ORANGE_MONEY', '+221 77 000 00 00'),
        'free_money' => env('AGENCE_FREE_MONEY', '+221 76 000 00 00'),
        'virement' => env('AGENCE_IBAN', 'SN000 00000 000000000000 00'),
    ],
];
