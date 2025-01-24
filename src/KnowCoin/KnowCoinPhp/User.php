<?php

namespace KnowCoin\KnowCoinPhp;

class User
{
    private string $name;
    private string $email;
    private string $type;
    private string $photo;
    private array $walletAddresses;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
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

}
