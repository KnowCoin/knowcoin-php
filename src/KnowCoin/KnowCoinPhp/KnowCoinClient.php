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
     * Search for profiles based on a query and type.
     *
     * @param string|null $query Optional. Search term to filter profiles by name or keyword.
     * @param string|null $type  Optional. Type of profiles to search for:
     *                           - `'individuals'` to return only individual profiles.
     *                           - `'businesses'` to return only business profiles.
     *                           - `null` (default) to return both individuals and businesses.
     *
     * @return array An array of mapped `Individual` or `Business` objects.
     *
     * @throws GuzzleException If the HTTP request fails.
     * @throws JsonException If JSON decoding fails.
     * @throws KnowCoinException If an error occurs while fetching profiles.
     *
     * @example
     *  $client = new KnowCoinClient();
     *
     *  // Search for all profiles (default: both individuals and businesses)
     *  $allProfiles = $client->searchProfiles();
     *  print_r($allProfiles);
     *
     *  // Search for profiles with the name "John Doe"
     *  $johnDoeProfiles = $client->searchProfiles('John Doe');
     *  print_r($johnDoeProfiles);
     *
     *  // Search for businesses only
     *  $businessProfiles = $client->searchProfiles(null, 'businesses');
     *  print_r($businessProfiles);
     *
     *  // Search for individuals named "Alice"
     *  $aliceProfiles = $client->searchProfiles('Alice', 'individuals');
     *  print_r($aliceProfiles);
     * /
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
     * Finds and returns a profile associated with the given cryptocurrency wallet address.
     *
     * This function sends a request to the KnowCoin API to retrieve profile details
     * linked to a specific wallet address. The profile can belong to either an individual
     * or a business. If no profile is found, the function returns `null`.
     *
     * @param string $walletAddress The cryptocurrency wallet address to look up.
     *
     * @return Individual|Business|null Returns an `Individual` or `Business` object if a profile is found,
     *                                  or `null` if no profile exists for the given wallet address.
     *
     * @throws KnowCoinException If an error occurs while fetching the profile from the API.
     * @throws GuzzleException If there is a failure in the HTTP request.
     * @throws JsonException If there is an error decoding the API response.
     *
     * @example
     * $client = new KnowCoinClient();
     * $profile = $client->findProfileByWalletAddress('0xABC123DEF458');
     *
     * if ($profile) {
     *     echo "Profile found: " . print_r($profile, true);
     * } else {
     *     echo "No profile found for this wallet address.";
     * }
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
