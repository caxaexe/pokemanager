<?php

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user']['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /pokemanager/public/login.php");
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: /pokemanager/public/?action=home");
        exit;
    }
}

function validatePokemon($name, $type, $generation, $category, $description, $abilities, $existingNames, $imageName): array {
    $errors = [];

    // Проверка имени
    if (empty($name)) {
        $errors['name'] = "Pokemon name is required.";
    } elseif (strlen($name) < 3) {
        $errors['name'] = "Pokemon name must be at least 3 characters long.";
    } elseif (in_array($name, $existingNames)) {
        $errors['name'] = "A Pokemon with that name already exists.";
    }

    // Проверка типов
    if (empty($type) || !is_array($type)) {
        $errors['type'] = "At least one type must be specified.";
    } elseif (count($type) > 2) {
        $errors['type'] = "You can select up to 2 types.";
    }

    // Проверка поколения
    if (empty($generation)) {
        $errors['generation'] = "Generation is required.";
    } elseif (!is_numeric($generation) || (int)$generation < 1) {
        $errors['generation'] = "Generation must be a number greater than 0.";
    }

    // Проверка категории (опционально)
    if (!empty($category) && strlen($category) > 255) {
        $errors['category'] = "Category is too long.";
    }

    // Проверка описания
    if (empty($description)) {
        $errors['description'] = "Description is required.";
    } elseif (strlen($description) < 10) {
        $errors['description'] = "The description must be at least 10 characters long.";
    }

    // Проверка способностей
    if (empty($abilities) || !is_array($abilities) || count(array_filter($abilities)) === 0) {
        $errors['abilities'] = "At least one ability must be specified.";
    }

    // Проверка изображения (опционально для теста)
    // if (!$imageName) {
    //     $errors['image'] = "Image not loaded.";
    // }

    if (!empty($errors)) {
        error_log("Validation errors: " . print_r($errors, true));
    }

    return $errors;
}

function getAllPokemonNames(PDO $pdo): array {
    try {
        $stmt = $pdo->query("SELECT name FROM pokemons");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        error_log("Error in getAllPokemonNames: " . $e->getMessage());
        return [];
    }
}

function getPokemonById(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM pokemons WHERE id = ?");
    $stmt->execute([$id]);
    $pokemon = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pokemon) {
        if (isset($pokemon['type']) && is_string($pokemon['type'])) {
            $pokemon['type'] = array_filter(array_map('trim', explode(',', $pokemon['type'])));
        }
        
        if (isset($pokemon['generations']) && is_string($pokemon['generations'])) {
            $pokemon['generations'] = array_filter(array_map('trim', explode(',', $pokemon['generations'])));
        }
        
        if (isset($pokemon['abilities']) && is_string($pokemon['abilities'])) {
            $pokemon['abilities'] = json_decode($pokemon['abilities'], true);
        }

        return $pokemon;
    }

    return null;
}