<?php

use App\Caller;

require 'vendor/autoload.php';

$users = (new Caller())->make('https://api.github.com/users', 'get')
    ->where('id', '>=', 20)
    ->where('login', '!=', 'kevinclark')
    ->where('id', '!=', 45)
    ->sort('id', 'desc')
    ->only(['login', 'id']);

var_dump($users);