<?php

namespace Tests\Unit;

use App\Caller;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class CallerTest extends TestCase
{
    /**
     * @return bool|string
     * @throws GuzzleException
     */
    public function getData()
    {
        return (new Caller())->make('https://api.github.com/users', 'get');
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function testWhereLoginIsNot()
    {
        $users = $this->getData()
            ->where('login', '!=', 'kevinclark')
            ->get();

        $logins = $this->getLogins($users);

        $this->assertNotContains('kevinclark', $logins);
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function testSorting()
    {
        $flag = true;

        $usersDefaultArray = $this->getData()
            ->only(['login']);

        $users = $this->getData()
            ->sort('login', 'desc')
            ->only(['login']);

        $logins = $this->getSortedValues($this->getLogins($usersDefaultArray), 'desc');

        foreach ($users as $key => $value) {
            if ($value['login'] !== $logins[$key])
                $flag = false;
        }

        $this->assertTrue($flag);
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function testWhereSorting()
    {
        $flag = true;

        $usersDefaultArray = $this->getData()
            ->only(['login', 'id']);

        $users = $this->getData()
            ->where('login', '!=', 'KirinDave')
            ->sort('id', 'desc')
            ->only(['login', 'id']);

        $ids = array_map(function ($user) {
            if ($user['login'] !== 'KirinDave') {
                return $user['id'];
            }
        }, $usersDefaultArray);

        $ids = $this->getSortedValues($ids, 'desc');

        foreach ($users as $key => $value) {
            if ($value['id'] !== $ids[$key] || $value['login'] === 'KirinDave')
                $flag = false;
        }

        $this->assertTrue($flag);
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function testWhereIdLessOrEqualThan()
    {
        $users = $this->getData()
            ->where('id', '<=', 30)
            ->only(['id']);

        $ids = $this->getIds($users);
        $maxId = max($ids);

        $this->assertLessThanOrEqual(30, $maxId);
    }

    /**
     * @param array $data
     * @param string $order
     * @return array
     */
    public function getSortedValues(array $data, string $order): array
    {
        $order === 'desc' ? arsort($data, SORT_REGULAR)
            : asort($data, SORT_REGULAR);

        return array_values($data);
    }

    /**
     * @param array $users
     * @return array
     */
    private function getLogins(array $users): array
    {
        $logins = [];

        foreach ($users as $user) {
            $logins[] = $user['login'];
        }

        return $logins;
    }

    /**
     * @param array $users
     * @return array
     */
    private function getIds(array $users): array
    {
        $ids = [];

        foreach ($users as $user) {
            $ids[] = $user['id'];
        }

        return $ids;
    }
}
