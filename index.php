<?php

// superapi config
$baseUrl = 'https://api-test.gpsuperapi.com/api';
$encryptKey = 'MNzLhy68lkH418xGYFE41XkKvoiRr2FX';
$operatorToken = '';
$seamlessOrSecretKey = '';
$playerUsername = '0801234567';
$currencyCode = 'THB';
$launchCode = 'da52e06c-d6b6-42c6-96b9-96b526bd82bd';

// superapi client
require_once __DIR__ . '/superapi.classes.php';

// superapi instance
$superapi = new SuperAPI($baseUrl, $encryptKey, $operatorToken, $seamlessOrSecretKey);

// get agent info
$agentInfo = $superapi->getAgentInfo();
print_r($agentInfo);

// get pr//oduct list
$productList = $superapi->getProductList();
print_r($productList);

// get game list
$gameList = $superapi->getGameList();
print_r($gameList);

// get game link
$gameLink = $superapi->getGameLink([
    'playerUsername' => $playerUsername,
    'returnUrl' => 'https://www.google.com',
    'playerIp' => '127.0.0.1',
    'launchCode' => $launchCode,
]);

print_r($gameLink);