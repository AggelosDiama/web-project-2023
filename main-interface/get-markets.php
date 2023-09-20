<?php
use Exception;
use MongoDB\Client;

require '../vendor/autoload.php'; // Include the MongoDB PHP driver

$mongoUrl = 'mongodb://localhost:27017'; 
$dbName = 'webproject2023'; 

$client = new MongoDB\Client($mongoUrl);
$collection = $client->$dbName->markets; 

$markets = $collection->find([]); // Retrieve all documents

$data = iterator_to_array($markets);

// foreach ($markets as $market) {
//     $data[] = [
//         'id' => $market['id'],
//         'name' => $market['name'],
//         'coordinates' => [
//             $market['coordinates'][0],
//             $market['coordinates'][1]
//         ],
//         'products' => $market['$products'],
//         'address' => $market['address']
//     ];
// }

header('Content-Type: application/json');
echo json_encode($data);
?>
