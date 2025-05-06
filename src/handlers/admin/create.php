<?php

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../src/helpers.php';



function createPokemon(PDO $pdo, array $postData): array {
    $name = trim($postData['name'] ?? '');
    $generation = trim($postData['generation'] ?? '');
    $category = trim($postData['category'] ?? '');
    $description = trim($postData['description'] ?? '');
    $type = array_filter(array_map('trim', $postData['type'] ?? [])); // Исправлено: types вместо type
    $abilities = array_values(array_filter(array_map('trim', $postData['abilities'] ?? []), fn($s) => $s !== ''));
    $weaknesses = array_filter(array_map('trim', $postData['weaknesses'] ?? []));
    $imageUrl = null;
    $image = $_FILES['image'] ?? null;

    $errors = validatePokemon($name, $type, $generation, $category, $description, $weaknesses, $image, $abilities);

    if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../public/assets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
    
        $filename = uniqid('poke_', true) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
        $destination = $uploadDir . $filename;
    
        if (move_uploaded_file($image['tmp_name'], $destination)) {
            $imageUrl = 'public/assets/' . $filename;
        } else {
            $errors['image'] = 'Failed to save the uploaded image.';
        }
    } elseif ($image && $image['error'] !== UPLOAD_ERR_NO_FILE) {
        // Ошибка при загрузке файла (но файл был передан)
        $errors['image'] = 'Error uploading image (code ' . $image['error'] . ').';
    } else {
        // Файл не выбран вовсе
        $errors['image'] = 'Please upload an image.';
    }

    if (!empty($errors)) {
        return [
            'errors' => $errors,
            'data' => compact('name', 'generation', 'category', 'description', 'type', 'abilities', 'weaknesses')
        ];
    }

    try {
        // Вставка покемона без столбца weaknesses
        $stmt = $pdo->prepare('
            INSERT INTO pokemons (name, generation, category, description, type, abilities, image, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $name,
            $generation,
            $category,
            $description,
            json_encode($type, JSON_UNESCAPED_UNICODE),
            json_encode($abilities, JSON_UNESCAPED_UNICODE),
            $imageUrl,
            date('Y-m-d H:i:s')
        ]);

        // Получаем ID только что созданного покемона
        $pokemonId = $pdo->lastInsertId();

        // Вставка слабостей в таблицу pokemon_weaknesses
        if (!empty($weaknesses)) {
            $stmtWeakness = $pdo->prepare('
                INSERT INTO pokemon_weaknesses (pokemon_id, weakness_id)
                VALUES (?, ?)
            ');
            foreach ($weaknesses as $weaknessId) {
                $stmtWeakness->execute([$pokemonId, $weaknessId]);
            }
        }

        return ['success' => true];
    } catch (PDOException $e) {
        $errors['database'] = 'Failed to save Pokémon: ' . $e->getMessage();
        return [
            'errors' => $errors,
            'data' => compact('name', 'generation', 'category', 'description', 'type', 'abilities', 'weaknesses')
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = createPokemon($pdo, $_POST);

    if (!empty($result['success'])) {
        header('Location: /pokemanager/public/?action=list');
        exit;
    }

    $errors = $result['errors'] ?? [];
    $old = $result['data'] ?? [];
}


