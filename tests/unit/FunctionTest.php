<?php

namespace Tests\Unit;

use App\Caller;
use PHPUnit\Framework\TestCase;

class FunctionTest extends TestCase
{
    public function getUsers()
    {
        return (new Caller())->make('https://api.github.com/users', 'get');
    }

    /**
     * @test
     */
    public function testCondition()
    {
        $flag = true;

        $users = $this->getUsers()
            ->where('login', '=', 'kevinclark')
            ->only(['id', 'login']);

        foreach ($users as $user) {
            if ($user[1] !== 'kevinclark')
                $flag = false;
        }

        $this->assertTrue(true ? $flag : false);
    }

    /**
     * @test
     */
    public function testSorting()
    {
        $flag = true;

        $usersDefaultArray = $this->getUsers()
            ->only(['login']);

        $users = $this->getUsers()
            ->sort('login', 'desc')
            ->only(['login']);

        $userLogins = array_map(function ($user) {
            return $user[0];
        }, $usersDefaultArray);

        arsort($userLogins);

        $userLogins = array_values($userLogins);

        foreach ($users as $key => $value) {
            if ($value[0] !== $userLogins[$key])
                $flag = false;
        }

        $this->assertTrue(true ? $flag : false);
    }

    /**
     * @test
     */
    public function testConditionWithSorting()
    {
        $flag = true;

        $usersDefaultArray = $this->getUsers()
            ->only(['login', 'id']);

        $users = $this->getUsers()
            ->where('login', '!=', 'KirinDave')
            ->sort('id', 'desc')
            ->only(['login', 'id']);

        $userIds = array_map(function ($user) {
            if ($user[0] !== 'KirinDave') {
                return $user[1];
            }
        }, $usersDefaultArray);

        arsort($userIds);

        $userIds = array_values($userIds);

        foreach ($users as $key => $value) {
            if ($value[1] !== $userIds[$key] || $value[0] === 'KirinDave')
                $flag = false;
        }

        $this->assertTrue(true ? $flag : false);
    }
}
