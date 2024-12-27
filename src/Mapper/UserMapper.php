<?php

namespace KnowCoin\KnowCoinPhp\Mapper;

use KnowCoin\KnowCoinPhp\User;

class UserMapper
{
    /**
     * @param array $data
     * @return User[]
     */
    public function mapToUsers(array $data): array
    {
        $users = [];
        foreach ($data as $userData) {
            $users[] = $this->mapToUser($userData);
        }
        return $users;
    }

    /**
     * @param array $data
     * @return User
     */
    public function mapToUser(array $data): User
    {
        $user = new User();
        $user->setName($data['name'] ?? '');
        $user->setEmail($data['email'] ?? '');
        $user->setType($data['type'] ?? '');
        $user->setPhoto($data['photo'] ?? '');
        $user->setWalletAddresses($data['wallet_addresses'] ?? []);

        return $user;
    }
}
