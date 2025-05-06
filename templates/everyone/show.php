<?php

ob_start();

// Получаем типы
$type = is_array($pokemon['type']) ? $pokemon['type'] : (empty($pokemon['type']) ? [] : explode(',', $pokemon['type']));
if (!empty($type)) {
    $placeholders = str_repeat('?,', count($type) - 1) . '?';
    $typeStmt = $pdo->prepare("SELECT name FROM types WHERE id IN ($placeholders)");
    $typeStmt->execute($type);
    $typeNames = $typeStmt->fetchAll(PDO::FETCH_COLUMN);
} else {
    $typeNames = [];
}



// Поколение
$generationId = $pokemon['generation'] ?? null;
$generation = '';
if ($generationId) {
    $stmt = $pdo->prepare("SELECT name FROM generations WHERE id = ?");
    $stmt->execute([$generationId]);
    $generation = $stmt->fetchColumn();
}

// Слабости
$weaknesses = is_array($pokemon['weaknesses']) ? $pokemon['weaknesses'] : explode(',', $pokemon['weaknesses'] ?? '');
$weaknessNames = [];
if (!empty($weaknesses)) {
    $placeholders = str_repeat('?,', count($weaknesses) - 1) . '?';
    $stmt = $pdo->prepare("SELECT name FROM weaknesses WHERE id IN ($placeholders)");
    $stmt->execute($weaknesses);
    $weaknessNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Способности
$abilities = is_array($pokemon['abilities']) ? $pokemon['abilities'] : explode('|', $pokemon['abilities'] ?? '');

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

    .pokemon-view {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 700px;
        margin: 0 auto;
    }

    p {
        margin: 10px 0;
    }

    strong {
        color: #34495e;
    }

    img {
        max-width: 200px;
        height: auto;
        display: block;
        margin: 10px auto;
        border-radius: 8px;
    }

    ul, ol {
        margin-left: 20px;
    }

    .actions {
        margin-top: 20px;
        text-align: center;
    }

    .actions a {
        margin: 0 10px;
        background-color: #3498db;
        color: white;
        padding: 10px 16px;
        border-radius: 6px;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .actions a:hover {
        background-color: #2980b9;
    }
</style>

<div class="pokemon-view">
    <h2><?= htmlspecialchars($pokemon['name']) ?></h2>

    <?php if (!empty($pokemon['image'])): ?>
        <img src="/pokemanager/uploads/<?= htmlspecialchars($pokemon['image']) ?>" alt="Pokemon Image">
    <?php endif; ?>

    <p><strong>Категория:</strong><br> <?= htmlspecialchars($category ?? 'Не указана') ?></p>
    <p><strong>Типы:</strong><br> <?= !empty($typeNames) ? implode(', ', $typeNames) : 'Не указаны' ?></p>
    <p><strong>Поколение:</strong><br> <?= htmlspecialchars($generation ?? 'Не указано') ?></p>
    <p><strong>Слабости:</strong><br> <?= !empty($weaknessNames) ? implode(', ', $weaknessNames) : 'Не указаны' ?></p>
    <p><strong>Описание:</strong><br> <?= nl2br(htmlspecialchars($pokemon['description'])) ?></p>

    <?php if (!empty($abilities)): ?>
        <p><strong>Способности:</strong></p>
        <ul>
            <?php foreach ($abilities as $ability): ?>
                <li><?= htmlspecialchars($ability) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>



    <div class="actions">
        <a href="/pokemanager/public/?action=edit&id=<?= $pokemon['id'] ?>">Редактировать</a>
        <a href="/pokemanager/public/?action=delete&id=<?= $pokemon['id'] ?>" onclick="return confirm('Удалить покемона?')">Удалить</a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../everyone/layout.php';
?>
