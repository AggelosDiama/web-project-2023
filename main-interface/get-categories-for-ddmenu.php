<?php

use MongoDB\Client;

require '../vendor/autoload.php';

$mongoUrl = 'mongodb://localhost:27017';
$dbName = 'webproject2023';

$client = new MongoDB\Client($mongoUrl);
$categoriesCollection = $client->$dbName->categories;

$categories = $categoriesCollection->find([], ['projection' => ['_id' => 0]]);

$categoryData = iterator_to_array($categories);

header('Content-Type: application/json');
echo json_encode($categoryData);
?>
