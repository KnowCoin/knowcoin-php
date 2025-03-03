# knowcoin-php
A PHP library for interacting with the KnowCoin API

Library is currently under development.

Planned released in 2025. 

Stay tuned for more: knowcoin.com

## Running Tests
You can run the unit tests for the project by executing the following commands:
```
composer install
composer test
```



## Basic Usage

To use the KnowCoinClient, make sure you have your environment set up with your **KNOWCOIN_API_KEY** and **KNOWCOIN_API_URL**.

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Exception\GuzzleException;
use KnowCoin\KnowCoinPhp\KnowCoinClient;

try {
    $client = new KnowCoinClient();

    // Search all profiles
    $profiles = $client->searchProfiles();
    print_r($profiles);

    // Search for profiles with the name 'John Doe'
    $profiles = $client->searchProfiles('John Doe');
    print_r($profiles);

    // Search for businesses only
    $profiles = $client->searchProfiles(null, 'businesses');
    print_r($profiles);

    // Search for individuals named 'Alice'
    $profiles = $client->searchProfiles('Alice', 'individuals');
    print_r($profiles);

    // Find a profile by wallet address
    $profile = $client->findProfileByWalletAddress('0xABC123DEF458');
    if ($profile) {
        echo "Profile found: " . print_r($profile, true);
    } else {
        echo "No profile found for this wallet address.";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} catch (GuzzleException $e) {
    echo "❌ Error with the HTTP request: " . $e->getMessage() . "\n";
}
