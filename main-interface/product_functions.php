<?php

use Exception;
use MongoDB\Client;

require '../vendor/autoload.php'; // Include the MongoDB PHP driver

session_start();

$received = $_POST['data']; 
$user_data = json_decode($received, true);

user_insert_offer();


function user_insert_offer() {
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023'; 

    $client = new MongoDB\Client($mongoUrl);

    $selectedProduct = $_POST['product_id'];
    $offerPrice = $_POST['offered_price'];
    $store_id = $_POST['store_id'];
    $previous_price = 0;

    // Find market coordinates and if the product already exists
    $collection = $client->$dbName->markets; 
    $markets = $collection->find([]); // Retrieve all documents
    $productFound = false;
    foreach ($markets as $markets) {
        if ($markets['id'] == $store_id) {
            $market_longitude = $markets['coordinates'][0];
            $market_latitude = $markets['coordinates'][1];
            // Finding if the product already exists
            foreach ($markets['products'] as $product) {
                if ($product['id'] == $selectedProduct) {
                    $previous_price = $product['price'];
                    $productFound = true;
                }
            }
            break;
        } 
    }

    // Find user coordinates
    $collection = $client->$dbName->users; 
    $users = $collection->find([]); // Retrieve all documents
    foreach ($users as $users) {
        if ($users['email'] == $_SESSION["email"]) {
            $user_longitude = $users['location'][0];
            $user_latitude = $users['location'][1];
            break;
        } 
    }

    // Check if user is close enough to the store
    $distance_from_store = calculateDistance($market_latitude, $market_longitude, $user_latitude, $user_longitude);
    if ($distance_from_store > 50) {
        // Return error code
        $responce = 500;
        echo json_encode($responce);
        exit();
    }

    // If offer does not already exist just add it. Else do compare prices and add bonus to the user.
    if ($productFound == true) {
        //  Add the product offer to the store
        $collection = $client->$dbName->markets;
        $filter = [
            'id' => intval($store_id),
            'products.id' => intval($selectedProduct)
        ];
        $update = [
            '$set' => [
                'products.$.price' => $offerPrice 
            ]
        ];
        $collection->updateOne($filter, $update);
        addPointsToUser($previous_price, $offerPrice);
    } else {
        //  Add the product offer to the store
        $collection = $client->$dbName->markets;
        $filter = ['id' => $store_id];
        $productData = [
            'id' => intval($selectedProduct),
            'price' => $offerPrice,
            'criteria_check' => true,
            'date_submitted' => date("YYYY-mm-dd"),
            'like_count' => 0,
            'dislike_count' => 0,
            'available' => true,
            'made_by_user_id' => intval($_SESSION["id"]),
        ];
        $update = [
            '$push' => [
                'products' => $productData
            ]
        ];
        $collection->updateOne($filter, $update);
    }

    // Add product deal history to user
    $collection = $client->$dbName->users;
    $filter = ['email' => $_SESSION["email"]];
    $productData = [
        'product_id' => intval($selectedProduct),
        'store_id' => intval($store_id),
        'date_submitted' => date("YYYY-mm-dd")
    ];
    $update = [
        '$push' => [
            'product_offers' => $productData
        ]
    ];
    $collection->updateOne($filter, $update);
    
    echo json_encode($productFound);
    exit();
}

function calculateDistance($lat1, $lon1, $lat2, $lon2) {

    // Convert latitude and longitude from degrees to radians
    $lat1 = deg2rad($lat1);
    $lon1 = deg2rad($lon1);
    $lat2 = deg2rad($lat2);
    $lon2 = deg2rad($lon2);

    // Radius of the Earth in kilometers
    $radius = 6371;

    // Special formula to calculate the distance, using the Haversine formula.
    $dlat = $lat2 - $lat1;
    $dlon = $lon2 - $lon1;
    $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Calculate the distance
    $distance = $radius * $c;
    $distance_in_meters = intval($distance * 1000); 
    return $distance_in_meters;   
}

function addPointsToUser($previous_price, $current_price) {
    if ($current_price < 0.2*$previous_price) {
        
    }
}