<?php

namespace KnowCoin\KnowCoinPhp\Mapper;

use KnowCoin\KnowCoinPhp\Individual;

class IndividualMapper
{
    /**
     * @param array $data
     * @return Individual
     */
    public function mapToIndividual(array $data): Individual
    {
        $individual = new Individual();
        $individual->setName($data['name'] ?? '');
        $individual->setEmail($data['email'] ?? '');
        $individual->setPhoto($data['photo'] ?? '');
        $individual->setWalletAddresses($data['wallet_addresses'] ?? []);
        $individual->setIsVerified($data['is_verified'] ? 'true' : 'false');

        return $individual;
    }
}
