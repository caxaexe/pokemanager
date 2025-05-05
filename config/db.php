<?php

$host = 'localhost';
$dbname = 'pokemanager';
$user = 'root';
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}


function getPdoConnection() {
    global $pdo;
    return $pdo;
}