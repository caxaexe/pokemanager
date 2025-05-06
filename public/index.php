<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/helpers.php';


if (!isset($pdo)) {
    echo "Подключение к базе данных не установлено!";
    exit;
}


$action = $_GET['action'] ?? 'home';

$data = $_POST ?? [];

switch ($action) {
    case 'create':
        require_once __DIR__ . '/../src/handlers/admin/create.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = createPokemon($pdo, $data);
        
            if (!empty($result['success'])) {
                $success = true;
                // Не перенаправляем — показываем сообщение
            } else {
                $errors = $result['errors'] ?? [];
                $data = $result['data'] ?? [];
                $success = false;
            }
        } else {
            $data = [];
            $errors = [];
            $success = null; // Форма ещё не отправлялась
        }

        // Включаем шаблон для создания заклинания
        include __DIR__ . '/../templates/admin/create.php';
        break;

    case 'edit':
        require_once __DIR__ . '/../src/handlers/admin/edit.php';

        $pokemon = getPokemonById($pdo, $id);

        include __DIR__ . '/../templates/admin/edit.php';
        break;

    
    case 'show':
        require_once __DIR__ . '/../src/helpers.php'; 
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo "No pokemon id found.";
            exit;
        }

        $pokemon = getPokemonById($pdo, $id);

        if (!$pokemon) {
            http_response_code(404);
            echo "Not found.";
            exit;
        }

        include __DIR__ . '/../templates/everyone/show.php';
        break;

    case 'delete':
        require_once __DIR__ . '/../src/handlers/admin/delete.php';
        $id = $_GET['id'] ?? null;

        if ($id) {
            deletePokemon($pdo, $id);
        }

        header('Location: /pokemanager/public/');
        exit;

        case 'home':
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limit = 5;
            $offset = ($page - 1) * $limit;

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
            $totalPokemons = (int) $countStmt->fetchColumn();
            $totalPages = ceil($totalPokemons / $limit);
        
            include __DIR__ . '/../templates/everyone/index.php';
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