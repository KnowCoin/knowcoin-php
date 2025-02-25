<?php

namespace KnowCoin\KnowCoinPhp\Mapper;

use KnowCoin\KnowCoinPhp\Business;

class BusinessMapper
{
    /**
     * @param array $data
     * @return Business
     */
    public function mapToBusiness(array $data): Business
    {
        $business = new Business();
        $business->setName($data['name'] ?? '');
        $business->setPhoto($data['photo'] ?? '');
        $business->setWalletAddresses($data['wallet_addresses'] ?? []);
        $business->setIsVerified($data['is_verified'] ? 'true' : 'false');
        $business->setBusinessIndustry($data['business_industry'] ?? '');
        $business->setBusinessType($data['business_type'] ?? '');

        return $business;
    }
}
