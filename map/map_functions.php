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
}


function update_user_location($latitude, $longitude) {
    $mongoUrl = 'mongodb://localhost:27017'; // Replace with your MongoDB connection URL
    $dbName = 'webproject2023'; // Replace with your database name

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 
    
    $filter = ['email' => $_SESSION["email"]];
    $update = [
        '$set' => [
            'location.0' => $longitude,
            'location.1' => $latitude
        ]
    ];

    $result = $collection->updateOne($filter, $update);
    echo json_encode("User location changed");
    exit();
}

function update_user_likes($change_type) {
    $mongoUrl = 'mongodb://localhost:27017'; // Replace with your MongoDB connection URL
    $dbName = 'webproject2023'; // Replace with your database name

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 

    $filter = ['email' => $_SESSION["email"]];
    
    
    # Find previous like count
    $user_to_change = $collection->findOne($filter);
    $user_like_count = $user_to_change["likes"];

    
    # Update user likes values
    if ($change_type === "add"){
        $new_like_count = $user_like_count + 1;
    } else $new_like_count = $user_like_count - 1;
    
    $update = ['$set' => ['likes' => $new_like_count]];
    $collection->updateOne($filter, $update);
    

    # Update store likes values
    // $collection = $client->$dbName->markets; 
    // $filter = ['email' => $_SESSION["email"]];
    // $update = ['$set' => ['likes' => $new_like_count]];
    // $collection->updateOne($filter, $update);
    
    echo json_encode("User like count changed");
    exit();
}

function update_user_dislikes($change_type) {
    $mongoUrl = 'mongodb://localhost:27017'; // Replace with your MongoDB connection URL
    $dbName = 'webproject2023'; // Replace with your database name

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 

    $filter = ['email' => $_SESSION["email"]];
    
    
    # Find previous like count
    $user_to_change = $collection->findOne($filter);
    $user_dislike_count = $user_to_change["dislikes"];

    
    # Update user likes values
    if ($change_type === "add"){
        $new_dislike_count = $user_dislike_count + 1;
    } else $new_dislike_count = $user_dislike_count - 1;
    
    $update = ['$set' => ['dislikes' => $new_dislike_count]];
    $collection->updateOne($filter, $update);
    

    # Update store likes values
    $collection = $client->$dbName->markets; 
    $filter = [
        'id' => $_POST["store_id"],
        'products' => [
            '$elemMatch' => [
                'id' => $_POST["product_id"] // Replace with the value you want to match within the array
            ]
        ]
    ];
    $update = [
        '$set' => [
            'products.$.dislike_count' => $new_dislike_count // Update the matched element's 'element1'
        ]
    ];
    $collection->updateOne($filter, $update);

    echo json_encode($update);
    exit();
}