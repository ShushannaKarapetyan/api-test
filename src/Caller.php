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
        $this->users = json_decode((new Client())->request($method, $api)->getBody());

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
        switch ($operator) {
            case '=':
                $this->users = array_filter($this->users, function ($user) use ($column, $value) {
                    if ($user->$column === $value) {
                        return $this->users[] = $user;
                    }
                });

                break;

            case ">":
                $this->users = array_filter($this->users, function ($user) use ($column, $value) {
                    if ($user->$column > $value) {
                        return $this->users[] = $user;
                    }
                });

                break;

            case "<":
                $this->users = array_filter($this->users, function ($user) use ($column, $value) {
                    if ($user->$column < $value) {
                        return $this->users[] = $user;
                    }
                });

                break;

            case ">=":
                $this->users = array_filter($this->users, function ($user) use ($column, $value) {
                    if ($user->$column >= $value) {
                        return $this->users[] = $user;
                    }
                });

                break;

            case "<=":
                $this->users = array_filter($this->users, function ($user) use ($column, $value) {
                    if ($user->$column <= $value) {
                        return $this->users[] = $user;
                    }
                });

                break;

            case '<>':
                $this->users = array_filter($this->users, function ($user) use ($column, $value) {
                    if ($user->$column <> $value) {
                        return $this->users[] = $user;
                    }
                });

                break;

            case "!=":
                $this->users = array_filter($this->users, function ($user) use ($column, $value) {
                    if ($user->$column !== $value) {
                        return $this->users[] = $user;
                    }
                });

                break;

            default:
                throw new Exception('Operator is not correct.');
        }

        return $this;
    }

    /**
     * @param $property
     * @param $sort
     * @return Caller
     * @throws Exception
     */
    public function sort($property, $sort)
    {
        $properties = [];
        $usersArray = [];

        foreach ($this->users as $key => $user) {
            if ($user->$property) {
                $properties[$user->id][] = $user->$property;
            }
        }

        switch ($sort) {
            case 'asc':
            case 'ASC':
                asort($properties);

                break;

            case "desc":
            case "DESC":
                arsort($properties);

                break;

            default:
                throw new Exception('Sorting value can be asc or desc.');
        }

        $userIds = array_keys($properties);

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
        return array_values($this->users);
    }

    /**
     * @param $properties
     * @return array
     */
    public function only($properties)
    {
        $users = [];

        foreach ($this->users as $key => $user) {
            foreach ($properties as $property) {
                $users[$key][] = $user->$property;
            }
        }

        return array_values($users);
    }
}