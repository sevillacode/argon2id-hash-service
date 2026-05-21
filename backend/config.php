<?php

return [
    'argon2id' => [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 1,
    ],

    'limits' => [
        'max_password_length' => 128,
        'max_hash_length' => 255,
    ],
];