<?php
use Exception;
use MongoDB\Client;

require 'vendor/autoload.php'; // Include the MongoDB PHP driver

$mongoUrl = 'mongodb://localhost:27017'; // Replace with your MongoDB connection URL
$dbName = 'webproject2023'; // Replace with your database name

$client = new MongoDB\Client($mongoUrl);
$collection = $client->$dbName->markets; // Replace 'markets' with your collection name

$markets = $collection->find([]); // Retrieve all documents

$data = [];
foreach ($markets as $market) {
    $data[] = [
        'name' => $market['name'],
        'coordinates' => [
            $market['coordinates'][0],
            $market['coordinates'][1]
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
?>
