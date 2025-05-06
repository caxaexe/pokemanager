<?php if (isset($_GET['error'])): ?>
    <div style="color: red; background-color: #fee; padding: 10px; border: 1px solid red; margin-bottom: 15px;">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<form action="../src/handlers/auth/register.php" method="POST">
    <h2>Регистрация</h2>
    
    <label>Имя пользователя:</label>
    <input type="text" name="username" required>

    <label>Пароль:</label>
    <input type="password" name="password" required>

    <label>Повторите пароль:</label>
    <input type="password" name="confirm_password" required>

    <input type="submit" value="Зарегистрироваться">
</form>
<p>Уже есть аккаунт? <a href="login.php">Войти</a></p>




<?php
$content = ob_get_clean();
include __DIR__ . '/../everyone/layout.php';
?>
