<?php

$config = require __DIR__ . '/../config/db.php';

try {
    $pdo = new PDO($config['dsn'], $config['user'], $config['password']);
    
  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('DB connection failed: ' . $e->getMessage());
}


return $pdo;