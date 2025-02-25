<?php

namespace KnowCoin\KnowCoinPhp\Tests;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use KnowCoin\KnowCoinPhp\Exceptions\KnowCoinException;
use KnowCoin\KnowCoinPhp\KnowCoinClient;
use KnowCoin\KnowCoinPhp\Mapper\IndividualMapper;
use KnowCoin\KnowCoinPhp\Mapper\BusinessMapper;
use KnowCoin\KnowCoinPhp\Individual;
use KnowCoin\KnowCoinPhp\Business;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class KnowCoinClientTest extends TestCase
{
    protected $mockHttpClient;
    protected $mockIndividualMapper;
    protected $mockBusinessMapper;
    protected KnowCoinClient $knowCoinClient;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->mockHttpClient = $this->createMock(Client::class);
        $this->mockIndividualMapper = $this->createMock(IndividualMapper::class);
        $this->mockBusinessMapper = $this->createMock(BusinessMapper::class);

        putenv('KNOWCOIN_API_KEY=test_api_key');
        putenv('KNOWCOIN_API_URL=https://api.knowcoin.com');

        $this->knowCoinClient = new KnowCoinClient(['httpClient' => $this->mockHttpClient]);
        $this->knowCoinClient->httpClient = $this->mockHttpClient;
        $this->knowCoinClient->individualMapper = $this->mockIndividualMapper;
        $this->knowCoinClient->businessMapper = $this->mockBusinessMapper;
    }

    public function test_constructor_throws_exception_without_api_key()
    {
        putenv('KNOWCOIN_API_KEY=');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('API key is required for KnowCoin API calls.');

        new KnowCoinClient();
    }

    public function test_constructor_throws_exception_without_url()
    {
        putenv('KNOWCOIN_API_URL=');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('KnowCoin URL is required for KnowCoin API calls.');

        new KnowCoinClient();
    }

    /**
     * @throws Exception
     * @throws KnowCoinException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function test_search_profiles_returns_mapped_profiles()
    {
        $mockResponseData = [
            'profiles' => [
                ['type' => 'Individual', 'name' => 'Alice'],
                ['type' => 'Business', 'name' => 'Tech Corp']
            ]
        ];

        $mockResponse = new Response(200, [], json_encode($mockResponseData));

        $this->mockHttpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/search')
            ->willReturn($mockResponse);

        $mockIndividual = $this->createMock(Individual::class);
        $mockBusiness = $this->createMock(Business::class);

        $this->mockIndividualMapper
            ->expects($this->once())
            ->method('mapToIndividual')
            ->with($mockResponseData['profiles'][0])
            ->willReturn($mockIndividual);

        $this->mockBusinessMapper
            ->expects($this->once())
            ->method('mapToBusiness')
            ->with($mockResponseData['profiles'][1])
            ->willReturn($mockBusiness);

        $result = $this->knowCoinClient->searchProfiles();

        $this->assertCount(2, $result);
        $this->assertSame($mockIndividual, $result[0]);
        $this->assertSame($mockBusiness, $result[1]);
    }

    /**
     * @throws Exception
     * @throws KnowCoinException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function test_find_profile_by_wallet_address_returns_mapped_individual()
    {
        $walletAddress = '0x123456';
        $mockResponseData = ['profile' => ['type' => 'Individual', 'name' => 'Alice']];

        $mockResponse = new Response(200, [], json_encode($mockResponseData));

        $this->mockHttpClient
            ->expects($this->once())
            ->method('get')
            ->with("/api/v1/crypto-address/{$walletAddress}")
            ->willReturn($mockResponse);

        $mockIndividual = $this->createMock(Individual::class);

        $this->mockIndividualMapper
            ->expects($this->once())
            ->method('mapToIndividual')
            ->with($mockResponseData['profile'])
            ->willReturn($mockIndividual);

        $result = $this->knowCoinClient->findProfileByWalletAddress($walletAddress);

        $this->assertInstanceOf(Individual::class, $result);
        $this->assertSame($mockIndividual, $result);
    }

    /**
     * @throws Exception
     * @throws KnowCoinException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function test_find_profile_by_wallet_address_returns_mapped_business()
    {
        $walletAddress = '0x789ABC';
        $mockResponseData = ['profile' => ['type' => 'Business', 'name' => 'Tech Corp']];

        $mockResponse = new Response(200, [], json_encode($mockResponseData));

        $this->mockHttpClient
            ->expects($this->once())
            ->method('get')
            ->with("/api/v1/crypto-address/{$walletAddress}")
            ->willReturn($mockResponse);

        $mockBusiness = $this->createMock(Business::class);

        $this->mockBusinessMapper
            ->expects($this->once())
            ->method('mapToBusiness')
            ->with($mockResponseData['profile'])
            ->willReturn($mockBusiness);

        $result = $this->knowCoinClient->findProfileByWalletAddress($walletAddress);

        $this->assertInstanceOf(Business::class, $result);
        $this->assertSame($mockBusiness, $result);
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function test_search_profiles_handles_request_exception()
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/search')
            ->willThrowException(new RequestException('API error', new Request('GET', '/api/v1/profiles/search')));

        $this->expectException(KnowCoinException::class);
        $this->expectExceptionMessage('Error fetching profiles: API error');

        $this->knowCoinClient->searchProfiles();
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function test_find_profile_by_wallet_address_handles_request_exception()
    {
        $walletAddress = '0x123456';

        $this->mockHttpClient
            ->expects($this->once())
            ->method('get')
            ->with("/api/v1/crypto-address/{$walletAddress}")
            ->willThrowException(new RequestException('API error', new Request('GET', "/api/v1/crypto-address/{$walletAddress}")));

        $this->expectException(KnowCoinException::class);
        $this->expectExceptionMessage("Error fetching profile for wallet address {$walletAddress}: API error");

        $this->knowCoinClient->findProfileByWalletAddress($walletAddress);
    }
}
