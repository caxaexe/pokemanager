<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../src/helpers.php';

// Проверка наличия подключения к базе данных
if (!isset($pdo)) {
    echo "Подключение к базе данных не установлено!";
    exit;
}

// Получаем действие из GET-запроса или по умолчанию 'home'
$action = $_GET['action'] ?? 'home';
// Получаем данные из POST-запроса
$data = $_POST ?? [];

// Обработка различных действий
switch ($action) {
    /**
     * Обрабатывает создание нового заклинания.
     *
     * При получении POST-запроса с данными создается новое заклинание.
     * Если создание прошло успешно, происходит редирект на главную страницу.
     * В случае ошибок отображаются соответствующие сообщения.
     */
    case 'create':
        require_once __DIR__ . '/../src/handlers/admin/create.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Создание заклинания с использованием данных POST
            $result = createPokemon($pdo, $data);

            if (!empty($result['success'])) {
                // Успешное создание, редирект на главную
                header('Location: /pokemanager/public/');
                exit;
            }

            // В случае ошибок отображаются данные и ошибки
            $errors = $result['errors'] ?? [];
            $data = $result['data'] ?? [];
        } else {
            $data = [];
            $errors = [];
        }

        // Включаем шаблон для создания заклинания
        include __DIR__ . '/../templates/admin/create.php';
        break;

    /**
     * Обрабатывает редактирование заклинания.
     *
     * При получении запроса редактируется заклинание с заданным ID.
     * Данные загружаются из базы данных и отображаются в форме для редактирования.
     */
    case 'edit':
        require_once __DIR__ . '/../src/handlers/admin/edit.php';

        // Получаем заклинание по ID
        $pokemon = getPokemonById($pdo, $id);

        // Включаем шаблон для редактирования заклинания
        include __DIR__ . '/../templates/admin/edit.php';
        break;

    /**
     * Показывает информацию о заклинании по ID.
     *
     * Если ID заклинания не передан или заклинание не найдено, возвращается ошибка.
     * В случае успеха отображается информация о заклинании.
     */
    case 'show':
        require_once __DIR__ . '/../src/helpers.php'; 
        $id = $_GET['id'] ?? null;

        if (!$id) {
            echo "ID заклинания не указано.";
            exit;
        }

        // Получаем заклинание по ID
        $pokemon = getPokemonById($pdo, $id);

        if (!$pokemon) {
            http_response_code(404);
            echo "Заклинание не найдено.";
            exit;
        }

        // Включаем шаблон для показа заклинания
        include __DIR__ . '/../templates/everyone/show.php';
        break;

    /**
     * Удаляет заклинание по ID.
     *
     * При получении ID заклинания оно удаляется из базы данных,
     * после чего происходит редирект на главную страницу.
     */
    case 'delete':
        require_once __DIR__ . '/../src/handlers/admin/delete.php';
        $id = $_GET['id'] ?? null;

        if ($id) {
            // Удаление заклинания по ID
            deletePokemon($pdo, $id);
        }

        // Редирект на главную страницу
        header('Location: /pokemanager/public/');
        exit;

    /**
     * Отображает главную страницу со списком заклинаний.
     *
     * Получаем список заклинаний с пагинацией, отображаем их на главной странице.
     * Расчитывается общее количество страниц.
     */
    case 'home':
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        // Получаем заклинания с ограничением по страницам
        $stmt = $pdo->prepare("SELECT * FROM pokemons ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $pokemons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Получаем общее количество заклинаний
        $countStmt = $pdo->query("SELECT COUNT(*) FROM pokemons");
        $totalPokemons = (int) $countStmt->fetchColumn();
        $totalPages = ceil($totalPokemons / $limit);

        // Включаем шаблон главной страницы
        include __DIR__ . '/../templates/everyone/index.php';
        break;

    /**
     * Обработка случая по умолчанию для несуществующего действия.
     *
     * При вызове несуществующего действия отображается ошибка 404.
     */
    default:
        http_response_code(404);
        $title = 'Страница не найдена';
        ob_start();
        echo "<h2>404 — Страница не найдена</h2>";
        $content = ob_get_clean();
        include __DIR__ . '/../templates/everyone/layout.php';
        break;
}