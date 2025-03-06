<?php
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Exception\GuzzleException;
use KnowCoin\KnowCoinPhp\KnowCoinClient;
use KnowCoin\KnowCoinPhp\Exceptions\KnowCoinException;

try {
    $client = new KnowCoinClient();

    $walletAddresses = [
        '0xABC123DEF458',
        '0xINVALIDADDRESS',
    ];

    foreach ($walletAddresses as $walletAddress) {
        echo "\n🔍 Searching for profile with wallet address: {$walletAddress}...\n";

        $profile = $client->findProfileByWalletAddress($walletAddress);

        if ($profile) {
            print_r($profile);
        } else {
            echo "⚠️ No profile found for this wallet address.\n";
        }
    }

} catch (KnowCoinException $e) {
    echo "❌ KnowCoin API Error: " . $e->getMessage() . "\n";
} catch (GuzzleException $e) {
    echo "❌ HTTP Request Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage() . "\n";
}
