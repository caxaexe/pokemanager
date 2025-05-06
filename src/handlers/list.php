<?php

require_once __DIR__ . '/../../../config/db.php';

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Получаем покемонов с типами
$query = '
    SELECT p.id, p.name, p.image AS image_url, GROUP_CONCAT(t.name SEPARATOR ", ") AS type
    FROM pokemons p
    LEFT JOIN types t ON FIND_IN_SET(t.id, p.type) > 0
    GROUP BY p.id
    ORDER BY p.name
    LIMIT :limit OFFSET :offset
';

$stmt = $pdo->prepare($query);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$pokemons = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Подсчёт общего количества для пагинации
$countStmt = $pdo->query('SELECT COUNT(*) FROM pokemons');
$totalCount = $countStmt->fetchColumn();
$totalPages = ceil($totalCount / $limit);

// Передаём данные в шаблон
$pageTitle = 'Pokémon List';
include __DIR__ . '/../../../templates/everyone/index.php';
