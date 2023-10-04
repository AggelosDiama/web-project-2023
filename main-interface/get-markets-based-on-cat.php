<?php

use MongoDB\Client;

require '../vendor/autoload.php';

$mongoUrl = 'mongodb://localhost:27017';
$dbName = 'webproject2023';

$searchInput = isset($_GET['searchInput']) ? urldecode($_GET['searchInput']) : '';

$client = new MongoDB\Client($mongoUrl);

$marketsCollection = $client->$dbName->markets;
$productsCollection = $client->$dbName->products;
$categoriesCollection = $client->$dbName->categories;

$selectedCategory = $categoriesCollection->findOne(['id' => $searchInput]); 

if ($selectedCategory) {
    $productsFromCat = $productsCollection->find(
        ['category' => $selectedCategory['id']], 
        [
            'projection' => [
                '_id' => 0,
                'id' => [
                    '$toInt' => '$id' // Convert the "id" field to an integer
                ]
            ]
        ]
    );

    $matchingProducts = iterator_to_array($productsFromCat);

    foreach ($matchingProducts as $product) {

        $productId = $product['id'];
        
        $markets = $marketsCollection->find(
            ['products.id' => $productId]);
        
        $matchingMarketDocs = iterator_to_array($markets);

        foreach ($matchingMarketDocs as $market) {
            // Save the market name and coordinates in the $matchingMarkets array
            $matchingMarkets[] = [
                'id' => $market['id'],
                'name' => $market['name'],
                'coords' => $market['coordinates'],
            ];
        }
    }
    
}

//var_dump($matchingProductIds);


header('Content-Type: application/json');
echo json_encode($matchingMarkets);
?>
