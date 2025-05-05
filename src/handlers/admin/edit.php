<?php

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../src/helpers.php';


function updatePokemon(PDO $pdo, $id, $data) {
    $title = $data['title'] ?? '';
    $category = $data['category'] ?? '';
    $description = $data['description'] ?? '';
    $tags = $data['tags'] ?? []; 
    $steps = $data['steps'] ?? [];

    
    $errors = validateSpell($title, $category, $description, $tags, $steps);

    if (empty($errors)) {
   
        $stmt = $pdo->prepare("UPDATE spells SET title = ?, category = ?, description = ?, tags = ?, steps = ? WHERE id = ?");
        $stmt->execute([
            $title,
            $category,
            $description,
            implode(',', $tags),  // Преобразуем массив тегов в строку
            json_encode($steps),  // Преобразуем шаги в JSON
            $id
        ]);
        return ['success' => true];
    }

    // Возвращаем ошибки в случае неудачной валидации
    return ['errors' => $errors];
}

$pdo = getPdoConnection();
$id = $_GET['id'] ?? null;

// Проверка, что ID заклинания передан
if (!$id) {
    echo "ID заклинания не указано.";
    exit;
}

// Получаем данные заклинания по ID
$spell = getSpellById($pdo, $id);

if (!$spell) {
    echo "Заклинание не найдено.";
    exit;
}

$errors = [];

// Обработка POST-запроса для обновления данных
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tagsArray = $_POST['tags'] ?? [];  
    $steps = array_filter($_POST['steps'] ?? [], fn($step) => trim($step) !== '');

    // Подготовка данных для обновления
    $data = [
        'title' => $_POST['title'] ?? '',
        'category' => $_POST['category'] ?? '',
        'description' => $_POST['description'] ?? '',
        'tags' => $tagsArray, 
        'steps' => $steps,    
    ];

    // Выполнение обновления
    $result = updateSpell($pdo, $id, $data);

    if (!empty($result['success'])) {
        header("Location: /list-of-spells/public/?action=show&id=$id");
        exit;
    }

    $errors = $result['errors'] ?? [];
    $spell = array_merge($spell, $data);
}

// Подключение шаблона для редактирования
include __DIR__ . '/../../../templates/spell/edit.php';