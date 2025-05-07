<?php


require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../src/helpers.php';

error_log("Session user: " . print_r($_SESSION['user'] ?? [], true));

// if (!isLoggedIn() || !isAdmin()) {
//     error_log("User not logged in or not admin");
//     $_SESSION['error'] = 'You must be an admin to create Pokémon';
//     header('Location: /pokemanager/public/login.php');
//     exit;
// }

function createPokemon(PDO $pdo, array $postData, array $files): array {
    error_log("POST data: " . print_r($postData, true));
    error_log("FILES: " . print_r($files, true));

    $name = trim($postData['name'] ?? '');
    $generation = trim($postData['generation'] ?? '');
    $category = trim($postData['category'] ?? '');
    $description = trim($postData['description'] ?? '');
    $typeIds = array_filter(array_map('trim', $postData['type'] ?? []));
    $abilities = array_values(array_filter(array_map('trim', $postData['abilities'] ?? []), fn($s) => $s !== ''));
    $weaknessIds = array_filter(array_map('trim', $postData['weaknesses'] ?? []));

    // Преобразуем ID типов в имена
    $types = [];
    if (!empty($typeIds)) {
        try {
            $placeholders = implode(',', array_fill(0, count($typeIds), '?'));
            $stmt = $pdo->prepare("SELECT name FROM types WHERE id IN ($placeholders)");
            $stmt->execute($typeIds);
            $types = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error fetching types: " . $e->getMessage());
            return ['errors' => ['type' => 'Failed to fetch types']];
        }
    }

    $existingNames = getAllPokemonNames($pdo);

    $imageName = null;
    if (isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $files['image']['tmp_name'];
        $imageOriginalName = $files['image']['name'];
        $imageExtension = pathinfo($imageOriginalName, PATHINFO_EXTENSION);
        $imageName = uniqid() . '.' . $imageExtension;
        $targetPath = __DIR__ . '/../../../public/assets/' . $imageName;

        if (!is_writable(dirname($targetPath))) {
            error_log("Directory not writable: " . dirname($targetPath));
            return ['errors' => ['image' => 'Cannot write to assets directory']];
        }

        if (!move_uploaded_file($imageTmpPath, $targetPath)) {
            error_log("Failed to move image to $targetPath");
            return ['errors' => ['image' => 'Failed to upload image']];
        }
    }

    error_log("Data before validation: " . print_r([
        'name' => $name,
        'types' => $types,
        'generation' => $generation,
        'category' => $category,
        'description' => $description,
        'abilities' => $abilities,
        'imageName' => $imageName
    ], true));

    $errors = validatePokemon($name, $types, $generation, $category, $description, $abilities, $existingNames, $imageName);
    if (!empty($errors)) {
        error_log("Validation errors: " . print_r($errors, true));
        return [
            'errors' => $errors,
            'data' => compact('name', 'generation', 'category', 'description', 'typeIds', 'abilities', 'weaknessIds')
        ];
    }

    try {
        $stmt = $pdo->prepare('
            INSERT INTO pokemons (name, generation, category, description, type, abilities, image, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $name,
            $generation,
            $category,
            $description,
            json_encode($types, JSON_UNESCAPED_UNICODE),
            json_encode($abilities, JSON_UNESCAPED_UNICODE),
            $imageName,
            date('Y-m-d H:i:s')
        ]);

        $pokemonId = $pdo->lastInsertId();
        error_log("Pokemon created with ID: $pokemonId");

        if (!empty($weaknessIds)) {
            $stmtWeakness = $pdo->prepare('INSERT INTO pokemon_weaknesses (pokemon_id, weakness_id) VALUES (?, ?)');
            foreach ($weaknessIds as $weaknessId) {
                $stmtWeakness->execute([$pokemonId, $weaknessId]);
                error_log("Inserted weakness ID: $weaknessId for Pokémon ID: $pokemonId");
            }
        }

        return ['success' => true];
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [
            'errors' => ['database' => 'Failed to save Pokémon: ' . $e->getMessage()],
            'data' => compact('name', 'generation', 'category', 'description', 'typeIds', 'abilities', 'weaknessIds')
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = createPokemon($pdo, $_POST, $_FILES);

    if (!empty($result['success'])) {
        $_SESSION['success'] = 'Pokémon created successfully';
        header('Location: /pokemanager/public/?action=home');
        exit;
    }

    $_SESSION['errors'] = $result['errors'] ?? [];
    $_SESSION['old'] = $result['data'] ?? [];
    error_log("Redirecting to create with errors: " . print_r($_SESSION['errors'], true));
    header('Location: /pokemanager/public/?action=create');
    exit;
}

include __DIR__ . '/../../../templates/admin/create.php';