<<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/helpers.php';

/**
 * Подключение к базе данных.
 *
 * @var PDO $pdo
 */
$pdo = getPdoConnection();

/**
 * Получение ID покемона из параметра запроса.
 *
 * @var int|null $id
 */
$id = $_GET['id'] ?? null;
if (!$id) {
    die("Missing Pokemon ID.");
}

/**
 * Получение данных покемона по ID.
 *
 * @var array|false $pokemon
 */
$stmt = $pdo->prepare('SELECT id, name FROM pokemons WHERE id = ?');
$stmt->execute([$id]);
$pokemon = $stmt->fetch(PDO::FETCH_ASSOC);

/**
 * Имя покемона для предзаполнения формы.
 *
 * @var string $name
 */
$name = $pokemon['name'] ?? '';

ob_start();
?>


<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f2f7fa;
        color: #333;
        padding: 20px;
    }

    h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    form {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        margin: 0 auto;
    }

    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
        color: #34495e;
    }

    input[type="text"] {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-sizing: border-box;
        font-size: 14px;
        background-color: #fafafa;
    }

    .error {
        color: red;
        font-size: 0.9em;
        margin-top: 5px;
    }

    button[type="submit"],
    .cancel-button {
        margin-top: 20px;
        display: inline-block;
        background-color: #3498db;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    button:hover,
    .cancel-button:hover {
        background-color: #2980b9;
    }

    .cancel-button {
        background-color: #e74c3c;
        margin-left: 10px;
    }

    .cancel-button:hover {
        background-color: #c0392b;
    }
</style>

<h2>Edit Pokemon Name</h2>

<form action="/pokemanager/public/?action=edit&id=<?= $pokemon['id'] ?>" method="post">
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($pokemon['name'] ?? '') ?>">
    <?php if (!empty($errors['name'])): ?>
        <p style="color:red"><?= htmlspecialchars($errors['name']) ?></p>
    <?php endif; ?>
    <br>
    <button type="submit">Update</button>
    <a href="index.php" class="cancel-button">Cancel</a>
</form>


<?php
/**
 * Вывод содержимого формы с layout.
 */
$content = ob_get_clean();
include __DIR__ . '/../everyone/layout.php';
?>
