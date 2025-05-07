<?php
session_start();

require_once dirname(__DIR__, 3) . '/config/db.php';

/**
 * Обрабатывает форму входа пользователя.
 * 
 * Получает имя пользователя и пароль из POST-запроса,
 * проверяет их в базе данных и устанавливает сессию при успешной аутентификации.
 */

// Получение данных из формы
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Проверка на пустые поля
if (empty($username) || empty($password)) {
    header('Location: /public/login.php?error=Please fill in all fields.');
    exit();
}

// Поиск пользователя по имени
$stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Проверка пароля
if (!$user || !password_verify($password, $user['password'])) {
    header('Location: /public/login.php?error=Incorrect username or password');
    exit();
}

// Установка пользовательской сессии
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

// Перенаправление после успешного входа
header('Location: /pokemanager/public/welcome.php');
exit();
