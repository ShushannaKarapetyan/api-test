<?php

require 'vendor/autoload.php';

use App\Caller;

$users = (new Caller())->make('https://api.github.com/users', 'get')
    ->where('id', '>=', 18)
    ->where('login', '!=', 'kevinclark')
    ->sort('id', 'desc')
    ->only(['login', 'id']);

var_dump($users);