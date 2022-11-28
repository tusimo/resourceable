<?php

declare(strict_types=1);
/**
 * This file is part of API Service.
 *
 * Please follow the code rules : PSR-2
 */
return [
    'repository' => [
        'cache' => [
            'table' => [
                'capacity' => 1024,
                'data_size' => 4098,
            ],
            'lru' => [
                'capacity' => 1024,
            ],
        ],
    ],
];
