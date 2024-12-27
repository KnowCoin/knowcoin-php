<?php

namespace KnowCoin\KnowCoinPhp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class KnowCoinClient
{
    protected $url;
    protected $httpClient;
    protected $apiKey;

    public function __construct(string $apiKey = null, array $config = [])
    {
        $this->apiKey =getenv('KNOWCOIN_API_KEY');
        $this->url = getenv('KNOWCOIN_URL');
        if (!$this->apiKey) {
            throw new \InvalidArgumentException('API key is required for KnowCoin API calls.');
        }
        $defaultConfig = [
            'base_uri' => $this->url,
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Accept' => 'application/json',
            ],
        ];

        $this->httpClient = new Client(array_merge($defaultConfig, $config));
    }

    public function searchProfiles(array $params): array
    {
        try {
            $response = $this->httpClient->get('/v1/profiles/search');
            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (RequestException $e) {
            throw new \Exception('Error during API request: ' . $e->getMessage(), $e->getCode());
        }
    }

    public function findProfileByWalletAddress(string $walletAddress): array
    {
        try {
            $response = $this->httpClient->get("/v1/crypto-address/{$walletAddress}");

            return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (RequestException $e) {
            throw new \Exception('Error during API request: ' . $e->getMessage(), $e->getCode());
        }
    }
}
