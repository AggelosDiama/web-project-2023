<?php

    //Connecting to the database
    $servername = "localhost:3306";
        $dbusername = "root";
        $dbpassword = "";
        $dbname = "";

    //Create connection
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    //Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

?>