<?php

namespace App;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Caller
{
    public $users;

    /**
     * @param $api
     * @param $method
     * @return bool|string
     * @throws GuzzleException
     */
    public function make($api, $method)
    {
        $this->users = (new Client())->request("$method", $api)->getBody();

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     * @return mixed
     * @throws Exception
     */
    public function where($column, $operator, $value)
    {
        $users = $this->users;

        if (gettype($this->users) === 'object') {
            $users = json_decode($this->users);
        }

        switch ($operator) {
            case '=':
                $this->users = array_filter(array_map(function ($user) use ($column, $value) {
                    if ($user->$column === $value) {
                        return $users[] = $user;
                    }
                }, $users));

                break;

            case ">":
                $this->users = array_filter(array_map(function ($user) use ($column, $value) {
                    if ($user->$column > $value) {
                        return $users[] = $user;
                    }
                }, $users));

                break;

            case "<":
                $this->users = array_filter(array_map(function ($user) use ($column, $value) {
                    if ($user->$column < $value) {
                        return $users[] = $user;
                    }
                }, $users));

                break;

            case ">=":
                $this->users = array_filter(array_map(function ($user) use ($column, $value) {
                    if ($user->$column >= $value) {
                        return $users[] = $user;
                    }
                }, $users));

                break;

            case "<=":
                $this->users = array_filter(array_map(function ($user) use ($column, $value) {
                    if ($user->$column <= $value) {
                        return $users[] = $user;
                    }
                }, $users));

                break;

            case '<>':
            case "!=":
                $this->users = array_filter(array_map(function ($user) use ($column, $value) {
                    if ($user->$column <> $value) {
                        return $users[] = $user;
                    }
                }, $users));

                break;

            default:
                throw new Exception('Operator is not correct.');
        }

        return $this;
    }

    /**
     * @param $parameter
     * @param $sort
     * @return Caller
     * @throws Exception
     * @retur n Caller
     */
    public function sort($parameter, $sort)
    {
        $parameters = [];
        $usersArray = [];

        foreach ($this->users as $key => $user) {
            $parameters[$user->id][] = $user->$parameter;
        }

        switch ($sort) {
            case 'asc':
            case 'ASC':
                asort($parameters);
                break;
            case "desc":
            case "DESC":
                arsort($parameters);
                break;
            default:
                throw new Exception('Sorting value can be asc or desc.');
        }

        $userIds = array_keys($parameters);

        foreach ($userIds as $id) {
            foreach ($this->users as $user) {
                if ($user->id === $id) {
                    $usersArray[] = $user;
                }
            }
        }

        $this->users = $usersArray;

        return $this;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->users;
    }

    /**
     * @param $parameters
     * @return array
     */
    public function only($parameters)
    {
        $users = [];

        foreach ($this->users as $key => $user) {
            foreach ($parameters as $parameter) {
                $users[$key][] =
                    $user->$parameter;
            }
        }

        return array_values($users);
    }
}