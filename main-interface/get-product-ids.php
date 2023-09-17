<?php

use Exception;
use MongoDB\Client;

require '../vendor/autoload.php';

$mongoUrl = 'mongodb://localhost:27017/'; 
$dbName = 'webproject2023'; 

// Get the storeName and marketId from the query parameters
//get-product-ids.php?marketId=${encodeURIComponent(marketId)}

$marketId = isset($_GET['marketId']) ? (int)$_GET['marketId'] : '';

$client = new MongoDB\Client($mongoUrl);
$marketsCollection = $client->$dbName->markets;

$market = $marketsCollection->findOne(array('id' => $marketId));

if($market){
    $data = [];
    $product_id_arr = $market['products'];
    
    foreach($product_id_arr as $product_id){
        $product_ids[] = $product_id['id'];

        }     
    
        $data = [
            'product-ids' => $product_ids,
            'product-info' => $market['products'],
        ];
} else {
    $data = ['error' => 'Store not found'];
}

header('Content-Type: application/json');
echo json_encode($data);
?>
