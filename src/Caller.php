<?php

namespace App;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Caller
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $availableMethods = [
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
    ];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Caller constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param string $api
     * @param string $method
     * @return Caller
     * @throws GuzzleException
     */
    public function make(string $api, string $method): Caller
    {
        $method = strtoupper($method);

        $this->checkMethod($method);

        $this->data = json_decode($this->client->request($method, $api)->getBody()->getContents(), true);

        return $this;
    }

    /**
     * @param string $path
     * @return Caller
     * @throws Exception
     */
    public function root(string $path): Caller
    {
        $segments = explode('.', $path);

        foreach ($segments as $segment) {
            if (!in_array($segment, array_keys($this->data))) {
                throw new Exception("The path \"{$segment}\" does not exist.", 400);
            }

            $this->data = $this->data[$segment];
        }

        return $this;
    }

    /**
     * @param string $param
     * @param string $operator
     * @param $value
     * @return mixed
     * @throws Exception
     */
    public function where(string $param, string $operator, $value): Caller
    {
        $this->checkParamExists(data_keys($this->data), $param);

        switch ($operator) {
            case '=':
                $this->data = array_filter($this->data, function ($user) use ($param, $value) {
                    if ($user[$param] === $value) {
                        return $this->data[] = $user;
                    }
                });

                break;

            case ">":
                $this->data = array_filter($this->data, function ($user) use ($param, $value) {
                    if ($user[$param] > $value) {
                        return $this->data[] = $user;
                    }
                });

                break;

            case "<":
                $this->data = array_filter($this->data, function ($user) use ($param, $value) {
                    if ($user[$param] < $value) {
                        return $this->data[] = $user;
                    }
                });

                break;

            case ">=":
                $this->data = array_filter($this->data, function ($user) use ($param, $value) {
                    if ($user[$param] >= $value) {
                        return $this->data[] = $user;
                    }
                });

                break;

            case "<=":
                $this->data = array_filter($this->data, function ($user) use ($param, $value) {
                    if ($user[$param] <= $value) {
                        return $this->data[] = $user;
                    }
                });

                break;

            case "!=":
                $this->data = array_filter($this->data, function ($user) use ($param, $value) {
                    if ($user[$param] !== $value) {
                        return $this->data[] = $user;
                    }
                });

                break;

            default:
                throw new Exception('The operator is not supported.', 400);
        }

        return $this;
    }

    /**
     * @param string $param
     * @param string $order
     * @return Caller
     * @throws Exception
     */
    public function sort(string $param, string $order): Caller
    {
        $params = [];
        $usersArray = [];

        $order = strtolower($order);

        if (!in_array($order, ['asc', 'desc'])) {
            throw new Exception("Supported sort directions are ASC and DESC.", 400);
        }

        $this->checkParamExists(data_keys($this->data), $param);

        foreach ($this->data as $key => $user) {
            $params[$user[$param]][] = $user[$param];
        }

        $order === 'desc' ? arsort($params, SORT_REGULAR)
            : asort($params, SORT_REGULAR);

        $userIds = array_keys($params);

        foreach ($userIds as $id) {
            foreach ($this->data as $user) {
                if ($user[$param] === $id) {
                    $usersArray[] = $user;
                }
            }
        }

        $this->data = $usersArray;

        return $this;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->data;
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function only(array $params): array
    {
        $users = [];

        foreach ($this->data as $key => $user) {
            foreach ($params as $param) {
                $this->checkParamExists(data_keys($this->data), $param);

                $users[$key][$param] = $user[$param];
            }
        }

        return $users;
    }

    /**
     * @param string $method
     * @throws Exception
     */
    private function checkMethod(string $method): void
    {
        if (!in_array($method, $this->availableMethods)) {
            throw new Exception("The method \"{$method}\" is not supported.", 400);
        }
    }

    /**
     * @param array $keys
     * @param string $param
     * @throws Exception
     */
    private function checkParamExists(array $keys, string $param): void
    {
        if (!in_array($param, $keys)) {
            throw new Exception("The parameter \"{$param}\" does not exist in current data list.", 400);
        }
    }
}