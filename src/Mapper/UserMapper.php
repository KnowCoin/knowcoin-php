<?php

namespace KnowCoin\KnowCoinPhp\Mapper;

use KnowCoin\KnowCoinPhp\Class\User;
use Tightenco\Collect\Support\Collection;

class UserMapper
{
    /**
     * @param array $data
     * @return Collection
     */
    public function mapToUsers(array $data): Collection
    {
        return collect($data)->map(fn($item) => $this->mapToUser($item));
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
