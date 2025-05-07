<?php
session_start();

require_once dirname(__DIR__, 3) . '/config/db.php';

/**
 * Обрабатывает регистрацию нового пользователя.
 *
 * Получает данные из POST-запроса, валидирует их, проверяет уникальность имени пользователя,
 * хеширует пароль и сохраняет нового пользователя в базу данных.
 * После успешной регистрации автоматически авторизует пользователя и перенаправляет его.
 */

// Получение данных из формы
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

// Проверка на пустые поля
if (empty($username) || empty($password) || empty($confirm)) {
    header('Location: /public/register.php?error=Заполните все поля');
    exit();
}

// Проверка совпадения паролей
if ($password !== $confirm) {
    header('Location: /public/register.php?error=Пароли не совпадают');
    exit();
}

// Проверка существования пользователя
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->fetch()) {
    header('Location: /public/register.php?error=Пользователь уже существует');
    exit();
}

// Хеширование пароля
$hash = password_hash($password, PASSWORD_DEFAULT);

// Добавление нового пользователя в базу данных
$stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->execute([$username, $hash, 'user']);

// Сохранение данных в сессии
$_SESSION['user_id'] = $pdo->lastInsertId();
$_SESSION['username'] = $username;
$_SESSION['role'] = 'user';

// Перенаправление на главную страницу
header('Location: /pokemanager/public/index.php');
exit();
