<?php

namespace KnowCoin\KnowCoinPhp;

class Business
{
    private string $name;
    private string $photo;
    private array $walletAddresses;
    private string $businessType;
    private string $businessIndustry;
    private string $isVerified;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): void
    {
        $this->photo = $photo;
    }

    public function getWalletAddresses(): array
    {
        return $this->walletAddresses;
    }

    public function setWalletAddresses(array $walletAddresses): void
    {
        $this->walletAddresses = $walletAddresses;
    }

    public function getBusinessType(): string
    {
        return $this->businessType;
    }

    public function setBusinessType(string $businessType): void
    {
        $this->businessType = $businessType;
    }

    public function getBusinessIndustry(): string
    {
        return $this->businessIndustry;
    }

    public function setBusinessIndustry(string $businessIndustry): void
    {
        $this->businessIndustry = $businessIndustry;
    }

    public function getIsVerified(): string
    {
        return $this->isVerified;
    }

    public function setIsVerified(string $isVerified): void
    {
        $this->isVerified = $isVerified;
    }

}
