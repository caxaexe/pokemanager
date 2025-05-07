<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/helpers.php';


// Проверка подключения к базе данных
if (!isset($pdo)) {
    die("Подключение к базе данных не установлено!");
}

// Функция для получения ID из GET-параметров
function getIdFromRequest() {
    return isset($_GET['id']) ? (int)$_GET['id'] : null;
}

// Функция для обработки ошибок
function handleError($message) {
    http_response_code(404);
    echo $message;
    exit;
}

$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'create':
        if (file_exists(__DIR__ . '/../src/handlers/admin/create.php')) {
            require_once __DIR__ . '/../src/handlers/admin/create.php';
        } else {
            handleError('Создание покемона невозможно.');
        }
        break;

    case 'edit':
        $id = getIdFromRequest();
        if (!$id) {
            handleError("No Pokémon ID provided.");
        }
        $pokemon = getPokemonById($pdo, $id);
        if (!$pokemon) {
            handleError("Pokemon not found.");
        }
        include __DIR__ . '/../templates/admin/edit.php';
        break;

    case 'show':
        $id = getIdFromRequest();
        if (!$id) {
            handleError("No Pokémon ID provided.");
        }
        $pokemon = getPokemonById($pdo, $id);
        if (!$pokemon) {
            handleError("Pokemon not found.");
        }
        include __DIR__ . '/../templates/everyone/show.php';
        break;

    case 'delete':
        $id = getIdFromRequest();
        if ($id) {
            require_once __DIR__ . '/../src/handlers/admin/delete.php';
            deletePokemon($pdo, $id);
        }
        header('Location: /pokemanager/public/');
        exit;

    case 'home':
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        try {
            $stmt = $pdo->prepare("
                SELECT p.id, p.name, p.image_url, GROUP_CONCAT(t.name SEPARATOR ', ') AS type
                FROM pokemons p
                LEFT JOIN types t ON FIND_IN_SET(t.id, p.type) > 0
                GROUP BY p.id
                ORDER BY p.name
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $pokemons = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $countStmt = $pdo->query("SELECT COUNT(*) FROM pokemons");
            $totalPokemons = (int)$countStmt->fetchColumn();
            $totalPages = ceil($totalPokemons / $limit);

            include __DIR__ . '/../templates/everyone/index.php';
        } catch (PDOException $e) {
            handleError("Ошибка выполнения запроса: " . $e->getMessage());
        }
        break;

    default:
        http_response_code(404);
        $title = 'Page not found';
        ob_start();
        echo "<h2>404 — Page not found</h2>";
        $content = ob_get_clean();
        include __DIR__ . '/../templates/everyone/layout.php';
        break;
}
