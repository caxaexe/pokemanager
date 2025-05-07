<?php
session_start();
require_once dirname(__DIR__, 3) . '/config/db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if (empty($username) || empty($password) || empty($confirm)) {
    header('Location: /public/register.php?error=Заполните все поля');
    exit();
}

if ($password !== $confirm) {
    header('Location: /public/register.php?error=Пароли не совпадают');
    exit();
}

// Проверка на существующего пользователя
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->fetch()) {
    header('Location: /public/register.php?error=Пользователь уже существует');
    exit();
}

// Хеширование и добавление в БД
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->execute([$username, $hash, 'user']);

$_SESSION['user_id'] = $pdo->lastInsertId();
$_SESSION['username'] = $username;
$_SESSION['role'] = 'user';

header('Location: ../../../public/index.php');
exit();
