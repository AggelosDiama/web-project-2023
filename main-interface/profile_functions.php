<?php

use Exception;
use MongoDB\Client;

require '../vendor/autoload.php'; // Include the MongoDB PHP driver

session_start();

$received = $_POST['data']; 
$user_data = json_decode($received, true);

if ($user_data["functionality"] == "username_change"){
    changeUsername($user_data["current_username"] , $user_data["new_username"] , $user_data["confirm_username"]);
} elseif ($user_data["functionality"] == "password_change"){
    changePassword($user_data["current_password"] , $user_data["new_password"] , $user_data["confirm_password"]);
} 

 
function changeUsername($old_username, $new_username, $confirm_username) {
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023';

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 
    $users = $collection->find([]); // Retrieve all documents


    foreach ($users as $users) {
        if ($users['email'] == $_SESSION["email"]) {
            // Check if the user entered the correct old username
            if ($users["username"] != $old_username) {
                $response = "Current username field is wrong. Please enter the username you are using now.";
                echo json_encode($response);
                exit();
            }
            else break;
        } 
    }

    if ($new_username == "") {
        $response = "Username cannot be empty";
        echo json_encode($response);
        exit();
    }

    // Check if the user entered the same username twice
    if ($new_username != $confirm_username) {
        $response = "Usernames do not match";
        echo json_encode($response);
        exit();
    }

    $filter = ['email' => $_SESSION["email"]];
    $update = [
        '$set' => [
            'username' => $new_username
        ]
    ];

    $collection->updateOne($filter, $update);
    $response = 200;
    echo json_encode($response);
    exit();
}

function changePassword($old_password, $new_password, $confirm_password) {
    $mongoUrl = 'mongodb://localhost:27017'; 
    $dbName = 'webproject2023';

    $client = new MongoDB\Client($mongoUrl);
    $collection = $client->$dbName->users; 
    $users = $collection->find([]); // Retrieve all documents


    foreach ($users as $users) {
        if ($users['email'] == $_SESSION["email"]) {
            $checkpassword = password_verify($old_password, $users['password']);
            if($checkpassword === false){
                $response = "Current password is wrong";
                echo json_encode($users['password']);
                exit();
            }
            else break;
        } 
    }
    
    if (passwordStrength($new_password) == false) {
        $response = "You need to put a stronger password";
        echo json_encode($response);
        exit();
    }

    // Check if the user entered the same username twice
    if ($new_password != $confirm_password) {
        $response = "Passwords do not match";
        echo json_encode($response);
        exit();
    }

    $new_hashed_pwd = password_hash($new_password, PASSWORD_DEFAULT);

    $filter = ['email' => $_SESSION["email"]];
    $update = [
        '$set' => [
            'password' => $new_hashed_pwd
        ]
    ];

    $collection->updateOne($filter, $update);
    $response = 200;
    echo json_encode($response);
    exit();
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