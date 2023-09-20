<?php

use Exception;
use MongoDB\Client;

require '../vendor/autoload.php'; // Include the MongoDB PHP driver

session_start();

$mongoUrl = 'mongodb://localhost:27017'; 
$dbName = 'webproject2023'; 

$client = new MongoDB\Client($mongoUrl);


$collection = $client->$dbName->markets; 
$markets = $collection->find([]); // Retrieve all documents

// Define the threshold date (1 week ago)
$thresholdDate = new DateTime("-1 week");

foreach ($markets as $markets) {
    foreach ($markets['products'] as $product) {
        $dateSubmitted = new DateTime($product['date_submitted']);
        if ($dateSubmitted < $thresholdDate) {
            $filter = [
                'id' => intval($markets['id'])
            ];
            $update = [
                '$pull' => [
                    'products' => ['id' => $product["id"]]
                ]
            ];
            $collection->updateOne($filter, $update);
        }
    }
}

$responce = 200;
echo json_encode($responce);
exit();

?>