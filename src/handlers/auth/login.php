<?php
session_start();
require_once '../../../config/bd.php';

$email = trim($_POST['email']);
$password = $_POST['password'];

if (!$email || !$password) {
    $_SESSION['error'] = "Заполните все поля.";
    header("Location: /login.php");
    exit;
}

// Получаем пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['error'] = "Неверный email или пароль.";
    header("Location: /login.php");
    exit;
}

// Авторизация
$_SESSION['user'] = [
    'id' => $user['id'],
    'email' => $user['email'],
    'role' => $user['role']
];

header("Location: /templates/everyone/index.php");
