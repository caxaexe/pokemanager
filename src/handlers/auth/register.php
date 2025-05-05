<?php
session_start();
require '../../../config/bd.php';

$username = $_POST['username'];
$password = $_POST['password'];

if (!$username || !$password) {
    die("Поля не должны быть пустыми");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
try {
    $stmt->execute([$username, $hash]);
    header("Location: /login.php");
} catch (PDOException $e) {
    die("Ошибка: пользователь с таким именем уже существует");
}
