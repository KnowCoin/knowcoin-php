<?php
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Exception\GuzzleException;
use KnowCoin\KnowCoinPhp\KnowCoinClient;

try {
    $client = new KnowCoinClient();

    echo "🔍 Searching all profiles...\n";
    $profiles = $client->searchProfiles();
    print_r($profiles);

    echo "\n🔍 Searching for 'John Doe'...";
    $profiles = $client->searchProfiles('John Doe');
    print_r($profiles);

    echo "\n🔍 Searching for Businesses only...";
    $profiles = $client->searchProfiles(null, 'businesses');
    print_r($profiles);

    echo "\n🔍 Searching for Individuals with name 'Alice'...";
    $profiles = $client->searchProfiles('Alice', 'individuals');
    print_r($profiles);

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} catch (GuzzleException $e) {
}
