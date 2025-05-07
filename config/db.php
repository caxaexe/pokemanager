<?php

// Параметры подключения к базе данных
$host = 'localhost';
$dbname = 'pokemanager';
$user = 'root';
$password = ''; 

try {
    // Создание подключения к базе данных с использованием PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    // Обработка ошибки подключения
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}

/**
 * Получает текущее PDO-подключение к базе данных.
 *
 * @global PDO $pdo Подключение, установленное ранее.
 * @return PDO Объект подключения к базе данных.
 */
function getPdoConnection(): PDO {
    global $pdo; 
    return $pdo;
}
