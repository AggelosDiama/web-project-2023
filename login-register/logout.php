<?php

session_start();


if (isset($_SESSION['user_id'])) {

    // Destroy the session
    session_destroy();
    
    header("Location: login.php");
    exit(); 
} else {
    echo "You are not logged in. Please go back to the login page";
}