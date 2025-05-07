<?php

/**
 * Создаёт подключение к базе данных с использованием конфигурации из db.php.
 *
 * @return PDO Возвращает объект PDO при успешном подключении.
 * @throws PDOException Если подключение не удалось, выбрасывается исключение.
 */

$config = require __DIR__ . '/../config/db.php';

try {
    $pdo = new PDO($config['dsn'], $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
} catch (PDOException $e) {
    // Останавливаем выполнение и выводим сообщение об ошибке
    die('DB connection failed: ' . $e->getMessage());
}
