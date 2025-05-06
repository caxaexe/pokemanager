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


function validatePokemon($name, $type, $generation, $category, $description, $weaknesses, $image, $abilities) {
    $errors = [];

    if (trim($name) === '') {
        $errors['name'] = "Enter name.";
    }

    if (!is_array($type) || count($type) === 0) {
        $errors['type'] = 'Select type.';
    }

    if (trim($generation) === '') {
        $errors['generation'] = 'Select generation.';
    }

    if (trim($category) === '') {
        $errors['category'] = "Enter category.";
    }
    
    if (trim($description) === '') {
        $errors['description'] = "Enter description.";
    }

    if (!is_array($weaknesses) || count($weaknesses) === 0) {
        $errors['weaknesses'] = 'Select weaknesses.';
    }

    if ($image['error'] !== UPLOAD_ERR_OK) {
        $errors['image'] = 'Error uploading image. Please try again.';
    } elseif ($image['size'] > 2000000) {  // 2 MB max size
        $errors['image'] = 'Image size must be less than 2MB.';
    } elseif (!file_exists($image['tmp_name']) || !in_array(mime_content_type($image['tmp_name']), ['image/jpeg', 'image/png', 'image/gif'])) {
        $errors['image'] = 'Only JPG, PNG, and GIF images are allowed.';
    }   elseif (!in_array(mime_content_type($image['tmp_name']), ['image/jpeg', 'image/png', 'image/gif'])) {
        $errors['image'] = 'Only JPG, PNG, and GIF images are allowed.';
    }

    if (!is_array($abilities) || count(array_filter($abilities, fn($st) => trim($st) !== '')) === 0) {
        $errors['abilities'] = 'Enter at least one ability.';
    }

    return $errors;
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
