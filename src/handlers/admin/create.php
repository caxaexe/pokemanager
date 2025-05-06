<?php

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../src/helpers.php';

function createPokemon(PDO $pdo, array $postData): array {
    // Получаем данные из формы
    $name = trim($postData['name'] ?? '');
    $generation = trim($postData['generation'] ?? '');
    $category = trim($postData['category'] ?? '');
    $description = trim($postData['description'] ?? '');
    $type = array_filter(array_map('trim', $postData['type'] ?? [])); // Исправлено: types вместо type
    $abilities = array_values(array_filter(array_map('trim', $postData['abilities'] ?? []), fn($s) => $s !== ''));
    $weaknesses = array_filter(array_map('trim', $postData['weaknesses'] ?? [])); // Получаем слабости

    // Получаем все имена покемонов из базы данных
    $existingNames = getAllPokemonNames($pdo); // функция, которая делает SELECT name FROM pokemon

    // Обработка изображения (должна быть ДО валидации!)
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageOriginalName = $_FILES['image']['name'];
        $imageExtension = pathinfo($imageOriginalName, PATHINFO_EXTENSION);
        $imageName = uniqid() . '.' . $imageExtension;
        $targetPath = __DIR__ . '/../../../public/assets/' . $imageName;

        move_uploaded_file($imageTmpPath, $targetPath);
    }

    // Валидация данных
    $errors = validatePokemon($name, $type, $generation, $category, $description, $abilities, $existingNames, $imageName);

    if (!empty($errors)) {
        return [
            'errors' => $errors,
            'data' => compact('name', 'generation', 'category', 'description', 'type', 'abilities', 'weaknesses')
        ];
    }

    try {
        // Вставляем покемона в таблицу pokemons
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
            $imageName,
            date('Y-m-d H:i:s')
        ]);

        // Получаем ID только что созданного покемона
        $pokemonId = $pdo->lastInsertId();

        // Вставляем слабости в таблицу pokemon_weaknesses
        if (!empty($weaknesses)) {
            $stmtWeakness = $pdo->prepare('
                INSERT INTO pokemon_weaknesses (pokemon_id, weakness_id)
                VALUES (?, ?)
            ');
            foreach ($weaknesses as $weaknessId) {
                // Для каждой слабости получаем её ID из таблицы weaknesses
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

?>
