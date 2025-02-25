<?php

namespace KnowCoin\KnowCoinPhp\Mapper;

use KnowCoin\KnowCoinPhp\Individual;

class IndividualMapper
{
    /**
     * @param array $data
     * @return Individual[]
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
     * @return Individual
     */
    public function mapToUser(array $data): Individual
    {
        $user = new Individual();
        $user->setName($data['name'] ?? '');
        $user->setEmail($data['email'] ?? '');
        $user->setPhoto($data['photo'] ?? '');
        $user->setWalletAddresses($data['wallet_addresses'] ?? []);
        $user->setIsVerified($data['is_verified'] ? 'true' : 'false');

        return $user;
    }
}
