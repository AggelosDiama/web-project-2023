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
} elseif ($_POST["functionality"] == "check_user_distance") {
    check_user_distance();
} elseif ($_POST["functionality"] == "product_availability") {
    update_product_availability($_POST["availability"]);
} elseif ($_POST["functionality"] == "check_if_admin") {
    if ($_SESSION["is_admin"]) {
        $responce = 1;
    } else $responce = 0;
    echo json_encode($responce);
    exit();
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
        'product_id' => intval($_POST["product_id"]),
        'date_submited' => date("Y-m-d")
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
        'product_id' => intval($_POST["product_id"]),
        'date_submited' => date("Y-m-d")
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

function update_product_availability($availability) {
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023'; 

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->markets; 
    $filter = [
        'id' => intval($_POST["store_id"]),
        'products.id' => intval($_POST["product_id"])
    ];
    $update = [
        '$set' => [
            'products.$.available' => (bool)$availability 
        ]
    ];
    $collection->updateOne($filter, $update);


    exit();
}

function check_user_distance() {
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023'; 

    $client = new MongoDB\Client($mongoUrl);

    // Find market coordinates and if the product already exists
    $collection = $client->$dbName->markets; 
    $markets = $collection->find([]); // Retrieve all documents
    foreach ($markets as $markets) {
        if ($markets['id'] == $_POST["store_id"]) {
            $market_longitude = $markets['coordinates'][0];
            $market_latitude = $markets['coordinates'][1];
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
        $responce = 0;
    } else $responce = 1;

    $data = [
        "user_distance" => $responce
    ];
    echo json_encode($data);
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