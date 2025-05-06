<?php

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user']['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /login.php");
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: /templates/everyone/index.php"); // или 403 страница
        exit;
    }
}


function validatePokemon($name, $type, $generation, $category, $description, $abilities, $existingNames, $imageName): array {
    $errors = [];

    if (strlen($name) < 3) {
        $errors[] = "Имя покемона должно быть не менее 3 символов.";
    }

    if (in_array($name, $existingNames)) {
        $errors[] = "Покемон с таким именем уже существует.";
    }

    if (empty($type) || !is_array($type)) {
        $errors[] = "Необходимо указать хотя бы один тип.";
    }

    if (!is_numeric($generation) || (int)$generation < 1) {
        $errors[] = "Поколение должно быть числом больше 0.";
    }

    if (strlen($description) < 10) {
        $errors[] = "Описание должно быть не короче 10 символов.";
    }

    if (empty($abilities) || !is_array($abilities)) {
        $errors[] = "Необходимо указать способности.";
    }

    if (!$imageName) {
        $errors[] = "Изображение не загружено.";
    }

    return $errors;
}

function getAllPokemonNames(PDO $pdo): array {
    $stmt = $pdo->query("SELECT name FROM pokemons");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
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
