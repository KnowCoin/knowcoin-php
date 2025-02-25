<?php

namespace KnowCoin\KnowCoinPhp\Tests;

use KnowCoin\KnowCoinPhp\Mapper\BusinessMapper;
use KnowCoin\KnowCoinPhp\Business;
use PHPUnit\Framework\TestCase;

class BusinessMapperTest extends TestCase
{

    protected $mockBusinessMapper;
    protected $mockBusiness;

    protected function setUp(): void
    {
        $this->mockBusinessMapper = $this->createMock(BusinessMapper::class);
        $this->mockBusiness = $this->createMock(Business::class);
    }

    public function testMapToBusiness(): void
    {
        $data = [
            'name' => 'Tech Corp',
            'photo' => 'photo_url',
            'wallet_addresses' => ['wallet1', 'wallet2'],
            'is_verified' => 'true',
            'business_industry' => 'Software Development',
            'business_type' => 'Private',
        ];


        $this->mockBusiness->method('getName')->willReturn($data['name']);
        $this->mockBusiness->method('getPhoto')->willReturn($data['photo']);
        $this->mockBusiness->method('getWalletAddresses')->willReturn($data['wallet_addresses']);
        $this->mockBusiness->method('getIsVerified')->willReturn($data['is_verified']);
        $this->mockBusiness->method('getBusinessIndustry')->willReturn($data['business_industry']);
        $this->mockBusiness->method('getBusinessType')->willReturn($data['business_type']);

        $this->mockBusinessMapper
            ->expects($this->once())
            ->method('mapToBusiness')
            ->with($data)
            ->willReturn($this->mockBusiness);

        $this->mockBusinessMapper->mapToBusiness($data);
        $this->assertInstanceOf(Business::class, $this->mockBusiness);
        $this->assertEquals($data['name'], $this->mockBusiness->getName());
        $this->assertEquals($data['photo'], $this->mockBusiness->getPhoto());
        $this->assertEquals($data['wallet_addresses'], $this->mockBusiness->getWalletAddresses());
        $this->assertEquals($data['is_verified'], $this->mockBusiness->getIsVerified());
        $this->assertEquals($data['business_industry'], $this->mockBusiness->getBusinessIndustry());
        $this->assertEquals($data['business_type'], $this->mockBusiness->getBusinessType());
    }

    public function testMapToBusinessHandlesMissingFields(): void
    {
        $data = [];

        $mapper = $this->createMock(BusinessMapper::class);
        $business = $mapper->mapToBusiness($data);

        $this->assertInstanceOf(Business::class, $business);
        $this->assertEquals('', $business->getName());
        $this->assertEquals('', $business->getPhoto());
        $this->assertEquals([], $business->getWalletAddresses());
        $this->assertEquals('', $business->getIsVerified());
        $this->assertEquals('', $business->getBusinessIndustry());
        $this->assertEquals('', $business->getBusinessType());
    }
}
