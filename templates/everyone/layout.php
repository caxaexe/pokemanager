<?php session_start(); ?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'PokeManager' ?></title>
    <style>
    body {
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f4f0;
        color: #333;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    header {
        background-color: #f1c40f;
        color: #fff;
        padding: 20px 40px;
        text-align: center;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        position: relative;
    }

    header h1 {
        margin: 0;
        font-size: 2.5rem;
        letter-spacing: 1px;
    }

    nav {
        position: absolute;
        top: 20px;
        left: 40px;
        display: flex;
        gap: 10px;
    }

    .nav-button {
        background-color: #DCDA87;
        color: #333;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .nav-button:hover {
        background-color: #b5b67a;
        color: #5e3370;
    }

    main {
        padding: 30px 40px;
        flex-grow: 1;
    }

    footer {
        background-color: #f1c40f;
        color: #fff;
        padding: 15px 40px;
        text-align: center;
        font-size: 0.9rem;
        box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.15);
        margin-top: auto;
    }

    a {
        text-decoration: none;
    }

    .button-container {
        display: flex;
        justify-content: center;
        gap: 15px; 
        margin-top: 20px;
    }

    .cancel-button {
        background-color: #DCDA87;
        color: #333;
        padding: 12px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .cancel-button:hover {
        background-color: rgb(216, 216, 170);
        color: #5e3370;
    }

    .submit-button {
        background-color: rgb(252, 252, 229);
        color: #333;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .submit-button:hover {
        background-color: rgb(166, 166, 149);
        color: #5e3370;
    }
</style>

</head>
<body>

<header>
<nav>
    <a href="/pokemanager/public/index.php"><button class="nav-button">Home</button></a>

    <?php if (!isset($_SESSION['user'])): ?>
        <a href="/pokemanager/public/login.php"><button class="nav-button">Login</button></a>
        <a href="/pokemanager/public/register.php"><button class="nav-button">Register</button></a>
        <a href="/pokemanager/public/?action=create"><button class="nav-button">Add Pokemon</button></a>
    <?php else: ?>
        <span style="color: #8e44ad; padding: 8px 10px;">
            <?= htmlspecialchars($_SESSION['user']['email']) ?>
        </span>
        <a href="/pokemanager/public/logout.php"><button class="nav-button">Logout</button></a>
    <?php endif; ?>
</nav>
    <h1>PokeManager</h1>
</header>

<main>
    <?= $content ?? '' ?>
</main>

<footer>
    <p>&copy; pupupup</p>
</footer>
</body>
</html>
