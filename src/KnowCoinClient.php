<?php

namespace KnowCoin\KnowCoinPhp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use KnowCoin\KnowCoinPhp\Mapper\UserMapper;

class KnowCoinClient
{
    protected string $url;
    protected Client $httpClient;
    protected string $apiKey;
    protected UserMapper $userMapper;

    public function __construct(string $apiKey = null, array $config = [])
    {
        $this->apiKey = getenv('KNOWCOIN_API_KEY');
        $this->url = getenv('KNOWCOIN_URL');
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

    public function searchProfiles(): array
    {
        try {
            $response = $this->httpClient->get('/api/v1/profiles/search');
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            return $this->userMapper->mapToUsers($data['users'] ?? []);
        } catch (RequestException $e) {
            throw new \Exception('Error during API request: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function findProfileByWalletAddress(string $walletAddress): ?User
    {
        try {
            $response = $this->httpClient->get("/api/v1/crypto-address/{$walletAddress}");
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            return $this->userMapper->mapToUser($data['user'] ?? []);
        } catch (RequestException $e) {
            throw new \Exception('Error during API request: ' . $e->getMessage(), $e->getCode());
        }
    }
}
