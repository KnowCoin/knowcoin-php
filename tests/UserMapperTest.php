<?php

namespace KnowCoin\KnowCoinPhp\Tests;

use KnowCoin\KnowCoinPhp\Mapper\UserMapper;
use KnowCoin\KnowCoinPhp\User;
use PHPUnit\Framework\TestCase;

class UserMapperTest extends TestCase
{
    public function testMapToUser(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'type' => 'premium',
            'photo' => 'photo_url',
            'wallet_addresses' => ['wallet1', 'wallet2'],
        ];

        $mapper = new UserMapper();
        $user = $mapper->mapToUser($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->getName());
        $this->assertEquals('john.doe@example.com', $user->getEmail());
        $this->assertEquals('premium', $user->getType());
        $this->assertEquals('photo_url', $user->getPhoto());
        $this->assertEquals(['wallet1', 'wallet2'], $user->getWalletAddresses());
    }

    public function testMapToUsers(): void
    {
        $data = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'type' => 'premium',
                'photo' => 'photo_url',
                'wallet_addresses' => ['wallet1', 'wallet2'],
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'type' => 'basic',
                'photo' => 'another_photo_url',
                'wallet_addresses' => ['wallet3'],
            ],
        ];

        $mapper = new UserMapper();
        $users = $mapper->mapToUsers($data);

        $this->assertCount(2, $users);
        $this->assertInstanceOf(User::class, $users[0]);
        $this->assertEquals('John Doe', $users[0]->getName());
        $this->assertEquals('Jane Smith', $users[1]->getName());
    }
}