<?php

namespace KnowCoin\KnowCoinPhp;

class Individual
{
    private string $name;
    private string $email;
    private string $photo;
    private array $walletAddresses;
    private string $isVerified;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
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

    public function getIsVerified(): string
    {
        return $this->isVerified;
    }

    public function setIsVerified(string $isVerified): void
    {
        $this->isVerified = $isVerified;
    }


}
