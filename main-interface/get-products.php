<?php

use MongoDB\Client;

require '../vendor/autoload.php';

$mongoUrl = 'mongodb://localhost:27017';
$dbName = 'webproject2023';

$marketId = isset($_GET['marketId']) ? $_GET['marketId'] : '';

$client = new MongoDB\Client($mongoUrl);

$usersCollection = $client->$dbName->users;
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
    
            // Fetch category information
            $category = $categoriesCollection->findOne(['id' => $categoryId]);
    
            if ($category) {
                // Extract the category name
                $categoryName = $category['name'];
            } else {
                $categoryName = 'Unknown Category';
            }
    
            // Initialize the subcategory name
            $subcategoryName = 'Unknown Subcategory';
    
            // Fetch subcategory information
            if (!empty($category['subcategories'])) {
                foreach ($category['subcategories'] as $subcategoryItem) {
                    if ($subcategoryItem['uuid'] === $subcategoryId) {
                        $subcategoryName = $subcategoryItem['name'];
                        break; // Stop searching once the correct subcategory is found
                    }
                }
            }

            $user = $usersCollection->findOne(['id' => $product_info['made_by_user_id']]);

            // Append product data to the $data array
            $data[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product_info['price'],
                'likes' => $product_info['like_count'],
                'dislikes' => $product_info['dislike_count'],
                'category' => $categoryName,
                'subcategory' => $subcategoryName,
                'available' => $product_info['available'],
                'madeByUser' => $user['username'],
                'userScore' => $user['tokens']['total_tokens'],
                'dateSubmitted' => $product_info['date_submitted'],
            ];
        }
    }
} else {
    $data = ['error' => 'Store not found'];
}

header('Content-Type: application/json');
echo json_encode($data);
?>
