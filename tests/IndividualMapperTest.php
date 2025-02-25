<?php

namespace KnowCoin\KnowCoinPhp\Tests;

use KnowCoin\KnowCoinPhp\Mapper\IndividualMapper;
use KnowCoin\KnowCoinPhp\Individual;
use PHPUnit\Framework\TestCase;

class IndividualMapperTest extends TestCase
{
    protected $mockIndividualMapper;
    protected $mockIndividual;

    protected function setUp(): void
    {
        $this->mockIndividualMapper = $this->createMock(IndividualMapper::class);
        $this->mockIndividual = $this->createMock(Individual::class);
    }

    public function testMapToIndividual(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'photo' => 'photo_url',
            'wallet_addresses' => [
                [
                    'wallet_address' => 'test-address',
                    'coin' => 'Ethereum (ETH)',
                    'network' => null,
                ],
                [
                    'wallet_address' => 'test-address-2',
                    'coin' => 'Bitcoin (BTC)',
                    'network' => null,
                ]
            ],
            'is_verified' => 'true',
        ];


        $this->mockIndividual->method('getName')->willReturn($data['name']);
        $this->mockIndividual->method('getEmail')->willReturn($data['email']);
        $this->mockIndividual->method('getPhoto')->willReturn($data['photo']);
        $this->mockIndividual->method('getWalletAddresses')->willReturn($data['wallet_addresses']);
        $this->mockIndividual->method('getIsVerified')->willReturn($data['is_verified']);

        $this->mockIndividualMapper
            ->expects($this->once())
            ->method('mapToIndividual')
            ->with($data)
            ->willReturn($this->mockIndividual);

        $this->mockIndividualMapper->mapToIndividual($data);
        $this->assertInstanceOf(Individual::class, $this->mockIndividual);
        $this->assertEquals($data['name'], $this->mockIndividual->getName());
        $this->assertEquals($data['email'], $this->mockIndividual->getEmail());
        $this->assertEquals($data['photo'], $this->mockIndividual->getPhoto());
        $this->assertEquals($data['wallet_addresses'], $this->mockIndividual->getWalletAddresses());
        $this->assertEquals($data['is_verified'], $this->mockIndividual->getIsVerified());
    }

    public function testMapToUserHandlesMissingFields(): void
    {
        $data = [];

        $mapper = $this->createMock(IndividualMapper::class);
        $individual = $mapper->mapToIndividual($data);

        $this->assertInstanceOf(Individual::class, $individual);
        $this->assertEquals('', $individual->getName());
        $this->assertEquals('', $individual->getEmail());
        $this->assertEquals('', $individual->getPhoto());
        $this->assertEquals([], $individual->getWalletAddresses());
        $this->assertEquals('', $individual->getIsVerified());
    }

}