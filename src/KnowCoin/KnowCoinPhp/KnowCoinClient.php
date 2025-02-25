<?php

namespace KnowCoin\KnowCoinPhp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use JsonException;
use KnowCoin\KnowCoinPhp\Mapper\BusinessMapper;
use KnowCoin\KnowCoinPhp\Mapper\IndividualMapper;
use KnowCoin\KnowCoinPhp\Exceptions\KnowCoinException;

class KnowCoinClient
{
    protected string $url;
    public Client $httpClient;
    protected string $apiKey;
    public IndividualMapper $individualMapper;
    public BusinessMapper $businessMapper;

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
        $this->individualMapper = new IndividualMapper();
        $this->businessMapper = new BusinessMapper();
    }

    /**
     * @param string|null $query
     * @param string|null $type
     * @return array
     * @throws GuzzleException
     * @throws JsonException
     * @throws KnowCoinException
     */
    public function searchProfiles(?string $query = null, ?string $type = null): array
    {
        try {
            $queryParams = [];
            if ($query) {
                $queryParams['query'] = $query;
            }
            if ($type) {
                $queryParams['type'] = $type;
            }
            $response = $this->httpClient->get('/api/v1/profiles/search', [
                'query' => $queryParams
            ]);
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            $profiles = [];

            foreach ($data['profiles'] ?? [] as $profile) {
                if ($profile['type'] === 'Business') {
                    $profiles[] = $this->businessMapper->mapToBusiness($profile);
                } elseif ($profile['type'] === 'Individual') {
                    $profiles[] = $this->individualMapper->mapToIndividual($profile);
                }
            }

            return $profiles;
        } catch (RequestException $e) {
            throw new KnowCoinException("Error fetching profiles: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws KnowCoinException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function findProfileByWalletAddress(string $walletAddress): Individual|Business|null
    {
        try {
            $response = $this->httpClient->get("/api/v1/crypto-address/{$walletAddress}");
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            if (!isset($data['profile']['type'])) {
                return null;
            }

            return $data['profile']['type'] == 'Business'
                ? $this->businessMapper->mapToBusiness($data['profile'])
                : $this->individualMapper->mapToIndividual($data['profile']);

        } catch (RequestException $e) {
            throw new KnowCoinException("Error fetching profile for wallet address {$walletAddress}: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
