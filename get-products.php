<?php

require 'vendor/autoload.php'; // Include the MongoDB PHP driver

$mongoUrl = 'mongodb://localhost:27017'; // Replace with your MongoDB connection URL
$dbName = 'webproject2023'; // Replace with your database name

$client = new MongoDB\Client($mongoUrl);
$marketsCollection = $client->$dbName->markets; // Replace 'markets' with your markets collection name
$productsCollection = $client->$dbName->products; // Replace 'products' with your products collection name
$categoriesCollection = $client->$dbName->categories; // Replace 'categories' with your categories collection name

$markets = $marketsCollection->find([]); // Retrieve all markets

$data = [];
foreach ($markets as $market) {
    $marketData = [
        'name' => $market['name'],
        'coordinates' => [
            $market['coordinates'][0],
            $market['coordinates'][1]
        ],
        'products' => [], // Initialize products array for this market
    ];

    // Get product IDs for the current market productIds
    $products_info = $market['products'];

    foreach ($products_info as $product_info ) {
        $market_product_info = [
            'productId' => $product_info['id'],
            'price' => $product_info['price'],
            'like_count' => $product_info['like_count'],
            'dislike_count' => $product_info['dislike_count'],
        ];
    }

    // Fetch products corresponding to the product IDs
    $productsCursor = $productsCollection->find(['id' => ['$in' => $market_product_info['productId']]]);

    foreach ($productsCursor as $product) {
        // Retrieve the product's category ID
        $categoryId = $product['category'];

        // Fetch category information, including its subcategory
        $category = $categoriesCollection->findOne(['_id' => $categoryId]);

        // Extract category and subcategory names from the fetched category
        $categoryName = $category['name'];
        $subcategoryName = $category['subcategory']; // Assuming 'subcategory' is a field within the category document

        // Add product data including category and subcategory information
        $marketData['products'][] = [
            'name' => $product['name'],
            'price' => $product['price'],
            'category' => $categoryName,
            'subcategory' => $subcategoryName,
        ];
    }

    $data[] = $marketData;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
