<?php

namespace App;

class Caller
{
    /**
     * Caller constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $api
     * @param $method
     * @return bool|string
     */
    public function make($api, $method)
    {
        $curl = curl_init($api);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 YaBrowser/16.3.0.7146 Yowser/2.5 Safari/537.36',
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    /**
     * @param $users
     * @param $position
     * @param $operator
     * @param $value
     * @return array
     */
    public function where($users, $position, $operator, $value)
    {
        /*$users = $this->make('https://api.github.com/users', 'get');*/

        $users = json_decode($users, true);

        $filteredUsers = [];

        if ($operator === '=') {
            foreach ($users as $user) {
                if ($user[$position] === $value) {
                    $filteredUsers[] = $user;
                }
            }
        }

        return $filteredUsers;
    }

    /**
     * @param $filteredUsers
     * @param $parameter
     * @param $sort
     * @return mixed
     */
    public function sort($filteredUsers, $parameter, $sort)
    {
        $logins = [];

        foreach ($filteredUsers as $user) {
            $logins[] = $user[$parameter];
        }

        switch ($sort) {
            case 'ASC':
                asort($logins);
                break;
            case "DESC":
                arsort($logins);
                break;
        }

        return $logins;
    }

    public function only($parameter)
    {
        //return $this->sort();
    }
}

$caller = new Caller();

$users = $caller->make('https://api.github.com/users', 'get');

$filteredUsers = $caller->where($users, 'site_admin', '=', false);

$caller->sort($filteredUsers, 'login', 'DESC');

$caller->only(['login']);
