<?php
use MongoDB\Client;

require 'vendor/autoload.php'; // Include the MongoDB PHP driver

$mongoUrl = 'mongodb://localhost:27017'; // Replace with your MongoDB connection URL
$dbName = 'webproject2023'; // Replace with your database name

$client = new MongoDB\Client($mongoUrl);
$marketsCollection = $client->$dbName->markets; // Replace 'markets' with your markets collection name
$productsCollection = $client->$dbName->products; // Replace 'products' with your products collection name

$markets = $marketsCollection->find([]); // Retrieve all markets

$data = [];
foreach ($markets as $market) {
    $marketData = [
        'name' => $market['name'],
        'coordinates' => [
            $market['coordinates'][0],
            $market['coordinates'][1]
        ]
    ];

    // Fetch products for the current market
    $products = $productsCollection->find(['market_id' => $market['_id']]); // Assuming 'market_id' links products to markets

    $productData = [];
    foreach ($products as $product) {
        $productData[] = [
            'name' => $product['name'],
            // Add other product details you want to include
        ];
    }

    // Include products data in the market data
    $marketData['products'] = $productData;

    $data[] = $marketData;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
