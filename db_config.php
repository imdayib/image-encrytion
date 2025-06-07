<?php
                                 // Database configuration
    $db_host = '127.0.0.1:3309'; // Change to your database host and port if necessary
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'image_encryption';

    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
}


//email : admin@example.com
// password : password


