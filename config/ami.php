<?php
// config/ami.php
return [
    'host' => env('AMI_HOST', '127.0.0.1'),
    'port' => env('AMI_PORT', 5038),
    'username' => env('AMI_USERNAME', 'admin'),
    'secret' => env('AMI_SECRET', 'admin'),
];