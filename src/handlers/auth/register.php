<?php
session_start();
require_once '../../../config/bd.php'; // подключение к БД

$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

if (!$email || !$password || !$confirm) {
    $_SESSION['error'] = "Все поля обязательны.";
    header("Location: /register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Невалидный email.";
    header("Location: /register.php");
    exit;
}

if ($password !== $confirm) {
    $_SESSION['error'] = "Пароли не совпадают.";
    header("Location: /register.php");
    exit;
}

// Проверка, существует ли email
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    $_SESSION['error'] = "Email уже используется.";
    header("Location: /register.php");
    exit;
}

// Хэшируем пароль
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Добавляем пользователя
$stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'user')");
$stmt->execute([$email, $hashed]);

header("Location: /login.php");
