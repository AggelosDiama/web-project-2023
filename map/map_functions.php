<?php

use Exception;
use MongoDB\Client;

require '../vendor/autoload.php'; // Include the MongoDB PHP driver

session_start();
$received = $_POST['data']; 
$user_data = json_decode($received, true);

if ($_POST["functionality"] == "user_location") {
    update_user_location($_POST["latitude"], $_POST["longitude"]);
} elseif ($_POST["functionality"] == "user_likes") {
    update_user_likes($_POST["change_type"]);
} elseif ($_POST["functionality"] == "user_dislikes") {
    update_user_dislikes($_POST["change_type"]);
} elseif ($_POST["functionality"] == "current_marker_location") {
    current_marker_location();
}

exit();

function update_user_location($latitude, $longitude) {
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023'; 

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 
    
    $filter = ['email' => $_SESSION["email"]];
    $update = [
        '$set' => [
            'location.0' => floatval($longitude),
            'location.1' => floatval($latitude)
        ]
    ];

    $result = $collection->updateOne($filter, $update);
    echo json_encode("User location changed");
    exit();
}

function update_user_likes($change_type) {
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023'; 

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 

    $filter = ['email' => $_SESSION["email"]];
    
    
    # Find previous like count
    $user_to_change = $collection->findOne($filter);
    $user_like_count = $user_to_change["likes"];

    
    # Update user likes values
    $productData = [
        'marker_id' => intval($_POST["store_id"]),
        'product_id' => intval($_POST["product_id"])
    ];
    if ($change_type === "add"){
        $new_like_count = $user_like_count + 1;
        # Add the product for user history
        $update = [
            '$push' => [
                'liked_products' => $productData
            ]
        ];
        $collection->updateOne($filter, $update);
    } else {
        $new_like_count = $user_like_count - 1;
        # Remove the user history of liking that product
        $update = [
            '$pull' => [
                'liked_products' => ['product_id' => intval($_POST["product_id"])]
            ]
        ];
        $collection->updateOne($filter, $update);
    }
    $update = ['$set' => ['likes' => $new_like_count]];
    $collection->updateOne($filter, $update);


    # Update store likes values
    $collection = $client->$dbName->markets; 
    $filter = [
        'id' => intval($_POST["store_id"]),
        'products.id' => intval($_POST["product_id"])
    ];
    $update = [
        '$set' => [
            'products.$.like_count' => $new_like_count 
        ]
    ];
    $collection->updateOne($filter, $update);
    
    echo json_encode("User like count changed");
    exit();
}

function update_user_dislikes($change_type) {
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023'; 

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 

    $filter = ['email' => $_SESSION["email"]];
    
    
    # Find previous like count
    $user_to_change = $collection->findOne($filter);
    $user_dislike_count = $user_to_change["dislikes"];

    
    # Update user dislikes values
    $productData = [
        'marker_id' => intval($_POST["store_id"]),
        'product_id' => intval($_POST["product_id"])
    ];
    if ($change_type === "add"){
        $new_dislike_count = $user_dislike_count + 1;
        # Add the product for user history
        $update = [
            '$push' => [
                'disliked_products' => $productData
            ]
        ];
        $collection->updateOne($filter, $update);
    } else {
        $new_like_count = $user_dislike_count - 1;
        # Remove the user history of liking that product
        $update = [
            '$pull' => [
                'disliked_products' => ['product_id' => intval($_POST["product_id"])]
            ]
        ];
        $collection->updateOne($filter, $update);
    }
    $update = ['$set' => ['dislikes' => $new_like_count]];
    $collection->updateOne($filter, $update);
    
    $update = ['$set' => ['dislikes' => $new_dislike_count]];
    $collection->updateOne($filter, $update);
    

    # Update store likes values
    $collection = $client->$dbName->markets; 
    $filter = [
        'id' => intval($_POST["store_id"]),
        'products.id' => intval($_POST["product_id"])
    ];
    $update = [
        '$set' => [
            'products.$.dislike_count' => $new_dislike_count
        ]
    ];
    $collection->updateOne($filter, $update);

    echo json_encode("User dislike count changed");
    exit();
}

function current_marker_location() {
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023'; 

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 
    $users = $collection->find([]); // Retrieve all documents

    foreach ($users as $users) {
        if ($users['email'] == $_SESSION["email"]) {
            $latitude = $users['location'][1];
            $longitude = $users['location'][0];
            break;
        }
    }
    
    $user_current_location = [
        "user_latitude" => $latitude,
        "user_longitude" => $longitude
    ];

    echo json_encode($user_current_location);
    exit();
}