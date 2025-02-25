<?php

namespace KnowCoin\KnowCoinPhp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use JsonException;
use KnowCoin\KnowCoinPhp\Mapper\UserMapper;
use KnowCoin\KnowCoinPhp\Exceptions\KnowCoinException;

class KnowCoinClient
{
    protected string $url;
    public Client $httpClient;
    protected string $apiKey;
    public UserMapper $userMapper;

    public function __construct(array $config = [])
    {
        $this->apiKey = getenv('KNOWCOIN_API_KEY');
        $this->url = getenv('KNOWCOIN_API_URL');
        if (!$this->apiKey) {
            throw new \InvalidArgumentException('API key is required for KnowCoin API calls.');
        }
        if (!$this->url) {
            throw new \InvalidArgumentException('KnowCoin URL is required for KnowCoin API calls.');
        }

        $defaultConfig = [
            'base_uri' => $this->url,
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Accept' => 'application/json',
            ],
        ];

        $this->httpClient = new Client(array_merge($defaultConfig, $config));
        $this->userMapper = new UserMapper();
    }

    /**
     * @return array
     * @throws GuzzleException
     * @throws KnowCoinException
     * @throws JsonException
     */
    public function searchProfiles(): array
    {
        try {
            $response = $this->httpClient->get('/api/v1/profiles/search');
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            return $this->userMapper->mapToUsers($data['users'] ?? []);
        } catch (RequestException $e) {
            throw new KnowCoinException("Error fetching profiles: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws KnowCoinException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function findProfileByWalletAddress(string $walletAddress): ?User
    {
        try {
            $response = $this->httpClient->get("/api/v1/crypto-address/{$walletAddress}");
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            return $this->userMapper->mapToUser($data['user'] ?? []);
        } catch (RequestException $e) {
            throw new KnowCoinException("Error fetching profile for wallet address {$walletAddress}: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
