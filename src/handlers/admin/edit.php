<?php

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../src/helpers.php';

$pdo = getPdoConnection();

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Missing Pokémon ID.");
}

function updatePokemon(PDO $pdo, int $id, array $postData): array {
    $name = trim($postData['name'] ?? '');
    $generation = trim($postData['generation'] ?? '');
    $category = trim($postData['category'] ?? '');
    $description = trim($postData['description'] ?? '');
    $type = array_filter(array_map('trim', $postData['type'] ?? []));
    $abilities = array_filter(array_map('trim', $postData['abilities'] ?? []));
    $weaknesses = array_filter(array_map('trim', $postData['weaknesses'] ?? []));

    $existingNames = getAllPokemonNames($pdo, $id); // exclude current name

    $stmt = $pdo->prepare('SELECT image_url FROM pokemons WHERE id = ?');
    $stmt->execute([$id]);
    $currentImage = $stmt->fetchColumn();

    $imageName = $currentImage;
    if (!empty($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
        $tmpPath = $_FILES['image_url']['tmp_name'];
        $originalName = $_FILES['image_url']['name'];
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $imageName = uniqid() . '.' . $extension;
        $targetPath = __DIR__ . '/../../../public/assets/' . $imageName;

        move_uploaded_file($tmpPath, $targetPath);

        // // (необязательно) удалить старое изображение
        // if ($currentImage && file_exists(__DIR__ . '/../../../public/assets/' . $currentImage)) {
        //     unlink(__DIR__ . '/../../../public/assets/' . $currentImage);
        // }
    }

    $errors = validatePokemon($name, $type, $generation, $category, $description, $abilities, $existingNames, $imageName);
    if (!empty($errors)) {
        return ['errors' => $errors, 'data' => compact('name', 'generation', 'category', 'description', 'type', 'abilities', 'weaknesses')];
    }

    try {
        $stmt = $pdo->prepare('
            UPDATE pokemons
            SET name = ?, generation = ?, category = ?, description = ?, type = ?, abilities = ?, image = ?
            WHERE id = ?
        ');
        $stmt->execute([
            $name,
            $generation,
            $category,
            $description,
            json_encode($type, JSON_UNESCAPED_UNICODE),
            json_encode($abilities, JSON_UNESCAPED_UNICODE),
            $imageName,
            $id
        ]);

        // Обновим слабости
        $pdo->prepare('DELETE FROM pokemon_weaknesses WHERE pokemon_id = ?')->execute([$id]);
        if (!empty($weaknesses)) {
            $stmtWeak = $pdo->prepare('INSERT INTO pokemon_weaknesses (pokemon_id, weakness_id) VALUES (?, ?)');
            foreach ($weaknesses as $weakId) {
                $stmtWeak->execute([$id, $weakId]);
            }
        }

        return ['success' => true];
    } catch (PDOException $e) {
        return ['errors' => ['database' => $e->getMessage()], 'data' => $postData];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = updatePokemon($pdo, (int)$id, $_POST);

    if (!empty($result['success'])) {
        header('Location: /pokemanager/public/?action=list');
        exit;
    }

    $errors = $result['errors'] ?? [];
    $old = $result['data'] ?? [];
}
?>
