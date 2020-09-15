<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Caller;

$users = (new Caller())->make('https://api.github.com/users', 'get')
    ->where('id', '>=', 18)
    ->where('login', '!=', 'kevinclark')
    ->sort('id', 'desc')
    ->only(['login', 'id']);

echo '<pre>' . json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>';
