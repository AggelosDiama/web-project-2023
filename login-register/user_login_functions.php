<?php

use Exception;
use MongoDB\Client;

require '../vendor/autoload.php'; // Include the MongoDB PHP driver

$received = $_POST['data']; 
$user_data = json_decode($received, true);

if ($user_data["functionality"] == "register"){
    $email = $user_data["email"];
    $password = $user_data["password"];
    $username = $user_data["username"];
    $password_conf = $user_data["password_conf"];

    if (invalidEmail($email) !== false) {
        $response = "Invalid email";
        echo json_encode($response);
        exit();
    }
    
    if (emailExists($email) == true) {
        $response = "Email already exists";
        echo json_encode($response);
        exit();
    }
    
    if (passwordStrength($password) == false) {
        $response = "You need to put a stronger password";
        echo json_encode($response);
        exit();
    }
    
    if (passwordMatch($password, $password_conf) !== true) {
        $response = "Passwords don't match";
        echo json_encode($response);
        exit();
    }
    
    createUser($username, $email, $password);

} elseif ($user_data["functionality"] == "login"){
    $email = $user_data["email"];
    $password = $user_data["password"];
    loginUser($email, $password);
} 


 
function invalidEmail($email) {
    if ($email == NULL) {
        echo json_encode("Please enter your email");
        exit();
    }
    if(!preg_match("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/", $email)) {
        $result = true;
    }
    else {
        $result = false;
    }
    return $result;
}

function passwordMatch($password, $password_conf) {
    if($password !== $password_conf) {
        $result = false;
    }
    else {
        $result = true;
    }
    return $result;
}

function passwordStrength($password) {
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        return false;
    } 
    else { 
        return true; 
    }
}

function emailExists($email) {
    
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023';

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 
    $users = $collection->find([]); // Retrieve all documents

    foreach ($users as $users) {
        if ($users['email'] == $email) {
            return true;
        }
    }

    return false;

}

function createUser($name, $email, $password) {
    
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023'; 

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 

    $sortQuery = ['_id' => -1];
    $lastUser = $collection->findOne([], ['sort' => $sortQuery]);
    $new_user_id = $lastUser["id"] + 1;
  
    // User data to insert
    $user = [
        'id' => $new_user_id,
        'username' => $name,
        'password' =>  password_hash($password, PASSWORD_DEFAULT),
        'email' => $email,
        "location"=> [21.735020,38.246273],
        "is_admin" => false,
        "tokens" => [
            "total_tokens"=> 100,
            "this_month_tokens"=> 0,
            "last_month_tokens"=> 0,
            "given_tokens"=> 0
        ],
        "likes" => 0,
        "liked_products" => [],
        "dislikes" => 0,
        "disliked_products" => [],
        "deals_made"=> 0,
        "product_offers"=> []
    ];

    // Insert the user document
    $insertResult = $collection->insertOne($user);

    // Check if the insertion was successful
    if ($insertResult->getInsertedCount() > 0) {
        $response = 201;
        echo json_encode($response);
        exit();
    } else {
        $response = "User insertion failed.";
        echo json_encode($response);
        exit();
    }

}

function  loginUser($email, $password) {
    
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023'; 

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 
    $users = $collection->find([]); // Retrieve all documents
    $email_found = false;


    foreach ($users as $users) {
        if ($users['email'] == $email) {
            $passwordHashed = $users['password'];
            $user_id = $users['id'];
            $username = $users['username'];
            $is_admin = $users['is_admin'];
            $latitude = $users['location'][1];
            $longitude = $users['location'][0];
            $email_found = true;
            break;
        } 
    }

    if (!$email_found) {
        $response = "[ERROR] Wrong email";
        echo json_encode($response);
        exit();
    }

    $checkpassword = password_verify($password, $passwordHashed);

    if($checkpassword === false){
        $response = "[ERROR] Wrong Password";
        echo json_encode($response);
        exit();
    } else if ($checkpassword === true ){
        session_start();
        $_SESSION["user_id"] = $user_id;
        $_SESSION["email"] = $email;
        $_SESSION["username"] = $username;
        $_SESSION["is_admin"] = $is_admin;
        $_SESSION["latitude"] = $latitude;
        $_SESSION["longitude"] = $longitude;
        $response = 202;
        echo json_encode($response);
        exit();
    }
}



