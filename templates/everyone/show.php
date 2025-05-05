<?php 
/**
 * Отображение подробной информации о заклинании.
 * Включает заголовок, описание, шаги, дату, категорию и теги.
 */
ob_start();

/**
 * @var array|string $pokemon['tags'] Список тегов в виде массива ID или строки, разделённой запятыми.
 * Преобразование в массив ID.
 * @var int[] $tags Массив ID тегов.
 */
$tags = is_array($pokemon['tags']) ? $pokemon['tags'] : (empty($pokemon['tags']) ? [] : explode(',', $pokemon['tags']));

/**
 * Получение названия категории по её ID.
 * @var int $categoryId ID категории заклинания.
 * @var string|false $category Название категории или false, если не найдено.
 */
$categoryId = $pokemon['category']; 
$stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->execute([$categoryId]);
$category = $stmt->fetchColumn();

/**
 * Получение названий тегов по их ID.
 * @var string[] $selectedTagNames Массив названий тегов.
 */
if (!empty($tags)) {
    $placeholders = str_repeat('?,', count($tags) - 1) . '?';
    $tagStmt = $pdo->prepare("SELECT name FROM tags WHERE id IN ($placeholders)");
    $tagStmt->execute($tags);
    $selectedTagNames = $tagStmt->fetchAll(PDO::FETCH_ASSOC);

    $selectedTagNames = array_column($selectedTagNames, 'name');
}
?>
<style>
    h2 {
        font-size: 2rem;
        color: #5d3a9b;
        margin-bottom: 20px;
    }

    p {
        font-size: 1.1rem;
        margin: 10px 0;
    }

    p strong {
        font-weight: bold;
        color: #5d3a9b;
    }

    ol {
        list-style-type: decimal;
        padding-left: 20px;
        margin: 0; 
        text-align: left;
    }

    ol li {
        margin: 5px 0;
        font-size: 1rem;
    }

    a {
        color: #5d3a9b;
        text-decoration: none;
        font-weight: bold;
    }

    a:hover {
        text-decoration: underline;
    }

    .actions {
        margin-top: 20px;
    }

    .actions a {
        margin-right: 10px;
    }
</style>

<h2><?= htmlspecialchars($pokemon['title']) ?></h2>

<p><strong>Категория:</strong> <br><?= htmlspecialchars($category) ?></p>
<p><strong>Описание:</strong> <br><?= nl2br(htmlspecialchars($pokemon['description'])) ?></p>
<p><strong>Теги:</strong> <br> 
    <?= !empty($selectedTagNames) ? implode(', ', $selectedTagNames) : 'Нет тегов' ?>
</p>
<p><strong>Дата создания:</strong> <br><?= htmlspecialchars($pokemon['created_at']) ?></p>
<p><strong>Шаги:</strong></p>
<div style="text-align: center;">
    <ol style="display: inline-block; text-align: left;">
        <?php foreach ($pokemon['steps'] as $step): ?>
            <li><?= htmlspecialchars($step) ?></li>
        <?php endforeach; ?>
    </ol>
</div>

<a href="/pokemanager/public/?action=edit&id=<?= $pokemon['id'] ?>">Редактировать</a> |
<a href="/pokemanager/public/?action=delete&id=<?= $pokemon['id'] ?>" onclick="return confirm('Удалить заклинание?')">Удалить</a>

<?php

// Завершение буферизации и подключение шаблона layout
$content = ob_get_clean();
include __DIR__ . '/../layout.php';

?>