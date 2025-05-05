<form action="/src/handlers/auth/login.php" method="post">
    <h2>Login</h2>
    <?php if (!empty($_SESSION['error'])): ?>
        <p style="color:red"><?= $_SESSION['error'] ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <input type="text" name="login" placeholder="Login" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form>


<?php
$content = ob_get_clean();
include __DIR__ . '/../everyone/layout.php';
?>
