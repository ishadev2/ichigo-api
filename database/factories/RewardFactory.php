<?php

use App\Models\Reward;

$factory->define(Reward::class, function () {
    return [
        'availableAt' => null, 
        'expiresAt' => null,
        'redeemedAt' => null
    ];
});
