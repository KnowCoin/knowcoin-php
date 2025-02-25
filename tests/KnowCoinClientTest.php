<?php

namespace KnowCoin\KnowCoinPhp\Tests;

use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use KnowCoin\KnowCoinPhp\Exceptions\KnowCoinException;
use KnowCoin\KnowCoinPhp\KnowCoinClient;
use KnowCoin\KnowCoinPhp\Mapper\UserMapper;
use KnowCoin\KnowCoinPhp\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class KnowCoinClientTest extends TestCase
{
    protected $mockHttpClient;
    protected $mockUserMapper;
    protected KnowCoinClient $knowCoinClient;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->mockHttpClient = $this->createMock(Client::class);
        $this->mockUserMapper = $this->createMock(UserMapper::class);

        putenv('KNOWCOIN_API_KEY=test_api_key');
        putenv('KNOWCOIN_API_URL=https://api.knowcoin.com');

        $this->knowCoinClient = new KnowCoinClient(['httpClient' => $this->mockHttpClient]);
        $this->knowCoinClient->httpClient = $this->mockHttpClient;
        $this->knowCoinClient->userMapper = $this->mockUserMapper;
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

    public function test_search_profiles_returns_mapped_users()
    {
        $mockResponseData = [
            'users' => [
                ['id' => 1, 'name' => 'Alice'],
                ['id' => 2, 'name' => 'Bob']
            ]
        ];

        $mockResponse = new Response(200, [], json_encode($mockResponseData));

        $this->mockHttpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/search')
            ->willReturn($mockResponse);

        $this->mockUserMapper
            ->expects($this->once())
            ->method('mapToUsers')
            ->with($mockResponseData['users'])
            ->willReturn(['user_1', 'user_2']);

        $result = $this->knowCoinClient->searchProfiles();

        $this->assertEquals(['user_1', 'user_2'], $result);
    }

    public function test_find_profile_by_wallet_address_returns_mapped_user()
    {
        $walletAddress = '0x123456';
        $mockResponseData = ['user' => ['id' => 1, 'name' => 'Alice']];

        $mockResponse = new Response(200, [], json_encode($mockResponseData));

        $this->mockHttpClient
            ->expects($this->once())
            ->method('get')
            ->with("/api/v1/crypto-address/{$walletAddress}")
            ->willReturn($mockResponse);

        $mockUser = $this->createMock(User::class);

        $this->mockUserMapper
            ->expects($this->once())
            ->method('mapToUser')
            ->with($mockResponseData['user'])
            ->willReturn($mockUser);

        $result = $this->knowCoinClient->findProfileByWalletAddress($walletAddress);

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($mockUser, $result);
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws KnowCoinException
     * @throws JsonException
     */
    public function test_search_profiles_handles_request_exception()
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/v1/profiles/search')
            ->willThrowException(new RequestException('API error', new Request('GET', '/api/v1/profiles/search')));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error fetching profiles: API error');

        $this->knowCoinClient->searchProfiles();
    }

    /**
     * @throws KnowCoinException
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

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Error fetching profile for wallet address {$walletAddress}: API error");

        $this->knowCoinClient->findProfileByWalletAddress($walletAddress);
    }
}
