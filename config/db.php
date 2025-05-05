<?php

$host = 'localhost';
$dbname = 'pokemanager';
$user = 'root';
$password = ''; 

try {
    // Подключение к базе данных с использованием PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Установка режима обработки ошибок
} catch (PDOException $e) {
    // Вывод ошибки при подключении к базе данных
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}

/**
 * Получает объект подключения PDO.
 *
 * Эта функция возвращает глобальный объект подключения к базе данных, который был установлен в
 * процессе подключения. Используется для взаимодействия с базой данных.
 *
 * @return PDO Возвращает объект подключения PDO для выполнения запросов к базе данных.
 */
function getPdoConnection() {
    global $pdo; // Используется глобальная переменная, содержащая подключение к базе данных
    return $pdo;
}