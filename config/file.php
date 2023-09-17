<?php

$base_dir = 'uploads';

return [
    'upload' => [
        'max_size'  => 51200,   // kb
        'max_width' => 1024,    // px
        'quality'   => 60,
        'paths' => [
            'default'  => $base_dir . DIRECTORY_SEPARATOR,
            'user'     => $base_dir . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR,
        ],
    ],
];
