<?php
    // connect to mySQL database
    $servername = "localhost";
    $username = "root";
    $password = "my1112025pw_";
    $dbname = "giftlistdb";

    // use pdo_mysql to connect to database
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully with PDO"; 
    }
    catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    $conn = null;
?>