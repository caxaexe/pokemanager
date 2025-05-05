<?php

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../src/helpers.php';



function createPokemon(PDO $pdo, array $postData): array {
    // Получение данных из POST
    $name = trim($postData['name'] ?? '');
    $generation = trim($postData['generation'] ?? '');
    $category = trim($postData['category'] ?? '');
    $description = trim($postData['description'] ?? '');
    $types = array_filter(array_map('trim', $postData['types'] ?? []));
    $abilities = array_values(array_filter(array_map('trim', $postData['abilities'] ?? []), fn($s) => $s !== ''));
    $weaknesses = array_filter(array_map('trim', $postData['weaknesses'] ?? []));  // Получение выбранных слабостей

    // Валидация данных
    $errors = validatePokemon($name, $generation, $category, $description, $types, $abilities, $weaknesses);

    // Обработка изображения
    $imageUrl = null;
    if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../public/assets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = uniqid('poke_', true) . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            $imageUrl = 'public/assets/' . $filename;
        } else {
            $errors['image'] = 'Failed to save the uploaded image.';
        }
    } else {
        $errors['image'] = 'Please upload an image.';
    }

    if (!empty($errors)) {
        return [
            'errors' => $errors,
            'data' => compact('name', 'generation', 'category', 'description', 'types', 'abilities', 'weaknesses')
        ];
    }

    // Вставка данных в базу
    $stmt = $pdo->prepare('
        INSERT INTO pokemons (name, generation, category, description, types, abilities, weaknesses, image, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');

    $stmt->execute([
        $name,
        $generation, // Без JSON, если один ID
        $category,
        $description,
        json_encode($types, JSON_UNESCAPED_UNICODE),
        json_encode($abilities, JSON_UNESCAPED_UNICODE),
        json_encode($weaknesses, JSON_UNESCAPED_UNICODE),  // Добавление слабостей
        $imageUrl,
        date('Y-m-d H:i:s')
    ]);

    return ['success' => true];
}


