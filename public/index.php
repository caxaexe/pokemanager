<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/helpers.php';

// Проверка подключения к базе данных
if (!isset($pdo)) {
    die("Подключение к базе данных не установлено!");
}

/**
 * Получает ID из параметра запроса GET.
 *
 * @return int|null ID, если указан, или null.
 */
function getIdFromRequest(): ?int {
    return isset($_GET['id']) ? (int)$_GET['id'] : null;
}

/**
 * Обрабатывает ошибку, отправляя 404 и сообщение.
 *
 * @param string $message Сообщение об ошибке.
 * @return void
 */
function handleError(string $message): void {
    http_response_code(404);
    echo $message;
    exit;
}

// Определение действия, запрошенного пользователем
$action = $_GET['action'] ?? 'home';

switch ($action) {

    case 'create':
        require_once __DIR__ . '/../src/handlers/admin/create.php';
        break;

    case 'plz_8':
        // Пример защиты маршрута по роли
        if ($_SESSION['role'] === 'admin') {
            header('Location: /welcome.php');
        }
        break;

    case 'edit':
        $id = getIdFromRequest();
        if (!$id) {
            handleError("No Pokemon ID provided.");
        }

        $pokemon = getPokemonById($pdo, $id);
        if (!$pokemon) {
            handleError("Pokemon not found.");
        }

        $errors = [];

        // Обработка отправки формы редактирования
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $errors = validatePokemonName($name);

            if (empty($errors)) {
                $stmt = $pdo->prepare("UPDATE pokemons SET name = ? WHERE id = ?");
                $stmt->execute([$name, $id]);

                if ($stmt->rowCount() > 0) {
                    header("Location: /pokemanager/public/?action=show&id=$id");
                    exit;
                } else {
                    $errors['name'] = "Name not changed (maybe same as current).";
                }

                $pokemon['name'] = $name;
            } else {
                $pokemon['name'] = $name;
            }
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
