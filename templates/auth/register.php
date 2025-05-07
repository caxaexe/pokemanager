<?php if (isset($_GET['error'])): ?>
    <div style="color: red; background-color: #fee; padding: 10px; border: 1px solid red; margin-bottom: 15px;">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f6f9;
        color: #333;
        padding: 40px;
        margin: 0;
    }

    h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    form {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        margin: 0 auto;
        font-size: 16px;
    }

    label {
        display: block;
        margin: 10px 0 5px;
        font-weight: bold;
        color: #34495e;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
        box-sizing: border-box;
    }

    input[type="submit"] {
        width: 100%;
        padding: 12px;
        background-color:rgb(218, 197, 38);
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: rgb(204, 181, 6);
    }

    p {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    p a {
        color: rgb(143, 133, 0);
        text-decoration: none;
    }

    p a:hover {
        text-decoration: underline;
    }

    .error-message {
        color: red;
        background-color: #fee;
        padding: 10px;
        border: 1px solid red;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 14px;
    }
</style>

<form action="../src/handlers/auth/register.php" method="POST">
    <h2>Register</h2>
    
    <label>Username:</label>
    <input type="text" name="username" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Confirm password:</label>
    <input type="password" name="confirm_password" required>

    <input type="submit" value="Register">
</form>
<p>Already have an account? <a href="login.php">Login</a></p>




<?php
$content = ob_get_clean();
include __DIR__ . '/../everyone/layout.php';
?>
