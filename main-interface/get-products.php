<?php

use MongoDB\Client;

require '../vendor/autoload.php';

$mongoUrl = 'mongodb://localhost:27017';
$dbName = 'webproject2023';

$marketId = isset($_GET['marketId']) ? $_GET['marketId'] : '';

$client = new MongoDB\Client($mongoUrl);
$marketsCollection = $client->$dbName->markets;
$productsCollection = $client->$dbName->products;
$categoriesCollection = $client->$dbName->categories;


$market = $marketsCollection->findOne(['id' => (int)$marketId]);

if ($market) {
    $data = [];

    $marketProducts = $market['products'];

    foreach ($marketProducts as $product_info) {
        $that_id = $product_info['id'];

        // Fetch the product using its ID
        $product = $productsCollection->findOne(['id' => (string)$that_id]);

        if ($product) {
            // Retrieve the product's category ID and subcategory ID
            $categoryId = $product['category'];
            $subcategoryId = $product['subcategory'];

            // Fetch category information, including its subcategory
            $category = $categoriesCollection->findOne(['id' => $categoryId]);
            $subcategory = $categoriesCollection->findOne(['subcategories.uuid' => $subcategoryId]);

            if ($category && $subcategory) {
                // Extract category and subcategory names from the fetched documents
                $categoryName = $category['name'];
                $subcategoryName = $subcategory['name'];
            } else {
                $categoryName = 'Unknown Category';
                $subcategoryName = 'Unknown Subcategory';
            }

            // Append product data to the $data array
            $data[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product_info['price'],
                'likes' => $product_info['like_count'],
                'dislikes' => $product_info['dislike_count'],
                'category' => $categoryName,
                'subcategory' => $subcategoryName,
            ];
        }
    }
} else {
    $data = ['error' => 'Store not found'];
}

header('Content-Type: application/json');
echo json_encode($data);
?>
