<?php


$category = $pokemon['category'] ?? 'Не указана';

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
    $stmt = $pdo->prepare("SELECT name FROM weaknesses WHERE name IN ($placeholders)");
    $stmt->execute($weaknesses);
    $weaknessNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

$weaknessesList = !empty($weaknessNames) ? implode(', ', $weaknessNames) : 'Не указаны';

// Способности
$abilities = is_array($pokemon['abilities']) ? $pokemon['abilities'] : explode('|', $pokemon['abilities'] ?? '');

?>
<style>
    body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f2f7fa;
    color: #333;
    padding: 20px;
    margin: 0;
    box-sizing: border-box;
}

h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 28px;
    font-weight: bold;
}

.pokemon-view {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    max-width: 700px;
    margin: 20px auto;
    font-size: 16px;
    line-height: 1.6;
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
    /* border: 2px solid #ddd; */
}

ul, ol {
    margin-left: 20px;
    padding-left: 0;
    list-style-type: none;
}

ul li, ol li {
    padding-left: 20px;
    position: relative;
}

ul li::before, ol li::before {
    position: absolute;
    left: 0;
    color: #3498db;
    font-size: 20px;
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
    font-weight: bold;
}

.actions a:hover {
    background-color: #2980b9;
}

@media (max-width: 768px) {
    .pokemon-view {
        padding: 15px;
        width: 90%;
    }

    h2 {
        font-size: 24px;
    }

    img {
        max-width: 150px;
    }
}

</style>

<div class="pokemon-view">
    <h2><?= htmlspecialchars($pokemon['name']) ?></h2>

    <?php if (!empty($pokemon['image_url'])): ?>
    <img src="/pokemanager/public/assets/<?= htmlspecialchars(basename($pokemon['image_url'])) ?>" alt="Pokemon Image">
    <?php else: ?>
        <p>Image not found.</p>
    <?php endif; ?>


    <p><strong>Category:</strong><br> <?= htmlspecialchars($category ?? 'Не указана') ?></p>
    <p><strong>Type:</strong><br> <?= !empty($typeNames) ? implode(', ', $typeNames) : 'Не указаны' ?></p>
    <p><strong>Generation:</strong><br> <?= htmlspecialchars($generation ?? 'Не указано') ?></p>
    
    <p><strong>Weaknesses:</strong><br> 
        <?= !empty($weaknessNames) ? implode(', ', $weaknessNames) : 'Не указаны' ?>
    </p>

    <p><strong>Description:</strong><br> <?= nl2br(htmlspecialchars($pokemon['description'])) ?></p>

    <?php if (!empty($abilities)): ?>
        <p><strong>Abilities:</strong></p>
        <ul>
            <?php foreach ($abilities as $ability): ?>
                <li><?= htmlspecialchars($ability) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="actions">
        <a href="/pokemanager/public/?action=edit&id=<?= $pokemon['id'] ?>">Edit</a>
        <a href="/pokemanager/public/?action=delete&id=<?= $pokemon['id'] ?>" onclick="return confirm('Delete Pokemon?')">Delete</a>
    </div>
</div>


<?php
$content = ob_get_clean();
include __DIR__ . '/../everyone/layout.php';
?>
