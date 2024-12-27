<?php

namespace KnowCoin\KnowCoinPhp\Mapper;
use KnowCoin\KnowCoinPhp\Class\User;

class UserMapper
{
    /**
     * @param array $data
     * @return User[]
     */
    public function mapToUsers(array $data): array
    {
        return array_map([$this, 'mapToUser'], $data);
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
        $user->setWalletAddresses($data['walletAddresses'] ?? []);

        return $user;
    }
}
