<?php

use MongoDB\Client;

require '../vendor/autoload.php';

$mongoUrl = 'mongodb://localhost:27017';
$dbName = 'webproject2023';

$userEmail = isset($_GET["userEmail"]) ? $_GET["userEmail"] : null;
//$userEmail = "lemon@mail.com";

if (!$userEmail) {
    // Handle the case where the user ID is not provided
    echo json_encode(["error" => "User email not found"]);
    exit;
}

$client = new MongoDB\Client($mongoUrl);

$usersCollection = $client->$dbName->users;
$productsCollection = $client->$dbName->products;
$marketsCollection = $client->$dbName->markets;

// Find the user by user ID
$user = $usersCollection->findOne(['email' => $userEmail]);
$userInfo=[];
$userLikedProducts = [];
$userDislikedProducts = [];
$userProductOffers = [];
$userTokens = $user["tokens"];

if (!$user) {
    // Handle the case where the user is not found
    echo json_encode(["error" => "User not found"]);
    exit;
}

foreach ($user['liked_products'] as $liked_product){
    $market = $marketsCollection->findOne(['id'=>$liked_product['marker_id']]);
    $product = $productsCollection->findOne(['id'=>(string)$liked_product['product_id']]);    

    if ($product && $market) {
        $userLikedProducts[] = [
            "action" => "Liked product",
            "market_name" => $market['name'],
            "product_name" => $product['name'],
            "date_submitted"=> $liked_product['date_submitted']
        ];
    }
}

foreach ($user['disliked_products'] as $disliked_product){
    $market = $marketsCollection->findOne(['id'=>$disliked_product['marker_id']]);
    $product = $productsCollection->findOne(['id'=>(string)$disliked_product['product_id']]);    

    if ($product && $market) {
        $userDislikedProducts[] = [
            "action" => "Disliked product",
            "market_name" => $market['name'],
            "product_name" => $product['name'],
            "date_submitted"=> $disliked_product['date_submitted']
        ];
    }
}

foreach ($user['product_offers'] as $product_offer){
    $market = $marketsCollection->findOne(['id'=>$product_offer['store_id']]);
    $product = $productsCollection->findOne(['id'=>(string)$product_offer['product_id']]);    

    if ($product && $market) {
        $userProductOffers[] = [
            "action" => "Submitted product",
            "market_name" => $market['name'],
            "product_name" => $product['name'],
            "date_submitted"=> $product_offer['date_submitted']
        ];
    }
}


$userInfo = [
    "liked_products" => $userLikedProducts,
    "disliked_products" => $userDislikedProducts,
    "product_offers" => $userProductOffers,
    "tokens" => $userTokens
];

header('Content-Type: application/json');
echo json_encode($userInfo);
?>
