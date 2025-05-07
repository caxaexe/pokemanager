<?php

/**
 * Проверяет, вошел ли пользователь в систему.
 *
 * @return bool
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

/**
 * Проверяет, является ли текущий пользователь администратором.
 *
 * @return bool
 */
function isAdmin(): bool {
    return isLoggedIn() && $_SESSION['user']['role'] === 'admin';
}

/**
 * Требует авторизации пользователя, иначе выполняет редирект.
 *
 * @return void
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header("Location: /pokemanager/public/login.php");
        exit;
    }
}

/**
 * Требует, чтобы пользователь был администратором, иначе выполняет редирект.
 *
 * @return void
 */
function requireAdmin(): void {
    if (!isAdmin()) {
        header("Location: /pokemanager/public/?action=home");
        exit;
    }
}

/**
 * Проверяет корректность имени покемона.
 *
 * @param string $name
 * @return array Массив ошибок
 */
function validatePokemonName($name): array {
    $errors = [];

    if (empty($name)) {
        $errors['name'] = 'Имя не может быть пустым.';
    } elseif (strlen($name) < 2) {
        $errors['name'] = 'Имя должно быть не короче 2 символов.';
    }

    return $errors;
}

/**
 * Проверяет все поля покемона на валидность.
 *
 * @param string $name
 * @param array $type
 * @param string $generation
 * @param string $category
 * @param string $description
 * @param array $abilities
 * @param array $existingNames
 * @param string|null $imageName
 * @return array Массив ошибок
 */
function validatePokemon($name, $type, $generation, $category, $description, $abilities, $existingNames, $imageName): array {
    $errors = [];

    if (empty($name)) {
        $errors['name'] = "Pokemon name is required.";
    } elseif (strlen($name) < 3) {
        $errors['name'] = "Pokemon name must be at least 3 characters long.";
    } elseif (in_array($name, $existingNames)) {
        $errors['name'] = "A Pokemon with that name already exists.";
    }

    if (empty($type) || !is_array($type)) {
        $errors['type'] = "At least one type must be specified.";
    } elseif (count($type) > 2) {
        $errors['type'] = "You can select up to 2 types.";
    }

    if (empty($generation)) {
        $errors['generation'] = "Generation is required.";
    } elseif (!is_numeric($generation) || (int)$generation < 1) {
        $errors['generation'] = "Generation must be a number greater than 0.";
    }

    if (!empty($category) && strlen($category) > 255) {
        $errors['category'] = "Category is too long.";
    }

    if (empty($description)) {
        $errors['description'] = "Description is required.";
    } elseif (strlen($description) < 10) {
        $errors['description'] = "The description must be at least 10 characters long.";
    }

    if (empty($abilities) || !is_array($abilities) || count(array_filter($abilities)) === 0) {
        $errors['abilities'] = "At least one ability must be specified.";
    }

    return $errors;
}

/**
 * Получает все имена покемонов из базы данных.
 *
 * @param PDO $pdo
 * @return array Массив имен покемонов
 */
function getAllPokemonNames(PDO $pdo): array {
    try {
        $stmt = $pdo->query("SELECT name FROM pokemons");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        error_log("Error in getAllPokemonNames: " . $e->getMessage());
        return [];
    }
}

/**
 * Получает информацию о покемоне по его ID.
 *
 * @param PDO $pdo
 * @param int $id
 * @return array|null Ассоциативный массив данных покемона или null, если не найден
 */
function getPokemonById(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM pokemons WHERE id = ?");
    $stmt->execute([$id]);
    $pokemon = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pokemon) {
        if (isset($pokemon['type']) && is_string($pokemon['type'])) {
            $pokemon['type'] = array_filter(array_map('trim', explode(',', $pokemon['type'])));
        }

        if (isset($pokemon['generation']) && is_string($pokemon['generation'])) {
            $pokemon['generation'] = trim($pokemon['generation']);
        }

        if (isset($pokemon['abilities']) && is_string($pokemon['abilities'])) {
            $pokemon['abilities'] = json_decode($pokemon['abilities'], true);
        }

        return $pokemon;
    }

    return null;
}
