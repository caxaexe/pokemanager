<?php if (isset($_GET['error'])): ?>
    <div style="color: red; background-color: #fee; padding: 10px; border: 1px solid red; margin-bottom: 15px;">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<form action="../src/handlers/auth/login.php" method="POST">
    <h2>Вход</h2>
    
    <label>Имя пользователя:</label>
    <input type="text" name="username" required>

    <label>Пароль:</label>
    <input type="password" name="password" required>

    <input type="submit" value="Войти">
</form>
<p>Нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>

<?php
$content = ob_get_clean();
include __DIR__ . '/../everyone/layout.php';
?>
