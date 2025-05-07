<?php
session_start();
require_once dirname(__DIR__, 3) . '/config/db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header('Location: /public/login.php?error=Please fill in all fields.');
    exit();
}

$stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// if (!$user || !password_verify($password, $user['password'])) {
//     header('Location: /public/login.php?error=Incorrect username or password');
//     exit();
// }

// Вход успешен
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

header('Location: ../../../public/index.php');
exit();
