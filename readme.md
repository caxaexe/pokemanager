# спаси и сохрани. пожалкста поставьте сем upd:получили восем

# Индивидуальная работа. PokeManager

## Инструкция по запуску
- Убедиться, что на устройстве установлен PHP: `php -v`
- Убедиться в работоспособности `phpmyadmin`
- В браузере перейти по ссылке: `http://localhost/pokemanager/`
  
## Описание проекта
```
config/
├── db.php            
├── auth.php           

public/
├── assets/
├── index.php
├── login.php
├── logout.php          
├── register.php     
├── welcome.php          

src/
├── handlers/
│   ├── auth/
│   │   ├── login.php  
│   │   ├── register.php
│   │   └── logout.php  
│   └── admin/
│       ├── create.php
│       └── delete.php
├── db.php
├── helpers.php        

templates/
├── admin/
│   ├── create.php
│   └── edit.php
├── everyone/
│   ├── show.php
│   ├── index.php
│   └── layout.php
├── auth/
│   ├── login.php    
│   └── register.php  
```

## Документация проекта

### config/db.php 
Файл для конфигурации подключения к базе данных
```php
<?php

// Параметры подключения к базе данных
$host = 'localhost';
$dbname = 'pokemanager';
$user = 'root';
$password = ''; 

try {
    // Создание подключения к базе данных с использованием PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    // Обработка ошибки подключения
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}

/**
 * Получает текущее PDO-подключение к базе данных.
 *
 * @global PDO $pdo Подключение, установленное ранее.
 * @return PDO Объект подключения к базе данных.
 */
function getPdoConnection(): PDO {
    global $pdo; 
    return $pdo;
}
```

### config/auth.php 
Файл для обработки настроек аутентификации и авторизации

### public/index.php
Главная страница сайта, отображающая основную информацию
```php
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
```

### public/login.php
Страница входа пользователя.
```php
<?php include '../templates/auth/login.php'; ?>
```

### public/register.php
Страница регистрации нового пользователя.
```php
<?php include '../templates/auth/register.php'; ?>
```

### public/logout.php 
Страница для выхода пользователя из системы.
```php
<?php
/**
 * Перенаправление на основной файл index.php.
 *
 * Этот скрипт выполняет редирект, обычно используется в корне папки,
 * чтобы автоматически переадресовать пользователя на главный маршрут.
 */

// Выполняем перенаправление на index.php
header("Location: ./index.php");
exit;
```

### public/welcome.php
Страница приветствия после успешного входа.
```php
<?php
// Начало буферизации вывода (обязательно перед выводом HTML)
ob_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добро пожаловать</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Добро пожаловать!</h1>
    <p>Вы успешно вошли в систему.</p>
    <a href="logout.php">Выйти</a>
</body>
</html>

<?php
/**
 * Окончание буферизации вывода и подключение шаблона для общего оформления.
 *
 * Буферизация используется для того, чтобы сначала собрать весь HTML-контент,
 * а затем передать его в шаблон. Это позволяет разделить логику и внешний вид страницы.
 */

// Завершаем буферизацию и сохраняем HTML-контент в переменной $content
$content = ob_get_clean();

// Подключаем общий шаблон для отображения страницы
include  '../templates/everyone/layout.php';
?>
```

### src/handlers/auth/login.php
Обработка входа пользователя
```php
<?php
session_start();

require_once dirname(__DIR__, 3) . '/config/db.php';

/**
 * Обрабатывает форму входа пользователя.
 * 
 * Получает имя пользователя и пароль из POST-запроса,
 * проверяет их в базе данных и устанавливает сессию при успешной аутентификации.
 */

// Получение данных из формы
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Проверка на пустые поля
if (empty($username) || empty($password)) {
    header('Location: /public/login.php?error=Please fill in all fields.');
    exit();
}

// Поиск пользователя по имени
$stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Проверка пароля
if (!$user || !password_verify($password, $user['password'])) {
    header('Location: /public/login.php?error=Incorrect username or password');
    exit();
}

// Установка пользовательской сессии
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

// Перенаправление после успешного входа
header('Location: /pokemanager/public/welcome.php');
exit();
```

### src/handlers/auth/register.php
Обработка регистрации нового пользователя
```php
<?php
session_start();

require_once dirname(__DIR__, 3) . '/config/db.php';

/**
 * Обрабатывает регистрацию нового пользователя.
 *
 * Получает данные из POST-запроса, валидирует их, проверяет уникальность имени пользователя,
 * хеширует пароль и сохраняет нового пользователя в базу данных.
 * После успешной регистрации автоматически авторизует пользователя и перенаправляет его.
 */

// Получение данных из формы
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

// Проверка на пустые поля
if (empty($username) || empty($password) || empty($confirm)) {
    header('Location: /public/register.php?error=Заполните все поля');
    exit();
}

// Проверка совпадения паролей
if ($password !== $confirm) {
    header('Location: /public/register.php?error=Пароли не совпадают');
    exit();
}

// Проверка существования пользователя
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->fetch()) {
    header('Location: /public/register.php?error=Пользователь уже существует');
    exit();
}

// Хеширование пароля
$hash = password_hash($password, PASSWORD_DEFAULT);

// Добавление нового пользователя в базу данных
$stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->execute([$username, $hash, 'user']);

// Сохранение данных в сессии
$_SESSION['user_id'] = $pdo->lastInsertId();
$_SESSION['username'] = $username;
$_SESSION['role'] = 'user';

// Перенаправление на главную страницу
header('Location: /pokemanager/public/index.php');
exit();
```

### src/handlers/auth/logout.php 
Обработка выхода пользователя из системы.
```php
<?php
session_start();

/**
 * Завершает пользовательскую сессию и перенаправляет на страницу входа.
 */

// Очищаем все данные сессии
session_unset();

// Уничтожаем сессию
session_destroy();

// Перенаправляем пользователя на страницу входа
header("Location: /login.php");
exit;
```

### src/handlers/admin/create.php
Создание новых объектов
```php
<?php

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../src/helpers.php';

// Логируем информацию о текущем пользователе
error_log("Session user: " . print_r($_SESSION['user'] ?? [], true));

/**
 * Создает нового покемона в базе данных.
 *
 * Этот метод обрабатывает POST данные формы, загружает изображение, валидирует данные и сохраняет покемона в базе данных.
 * Также обрабатывает типы покемонов и их слабости.
 *
 * @param PDO $pdo Подключение к базе данных.
 * @param array $postData Данные, полученные из формы.
 * @param array $files Данные, связанные с загрузкой файлов (изображения).
 * @return array Массив с результатом операции и ошибками, если они есть.
 */
function createPokemon(PDO $pdo, array $postData, array $files): array {
    // Логирование данных, полученных из формы
    error_log("POST data: " . print_r($postData, true));
    error_log("FILES: " . print_r($files, true));

    // Извлечение и очистка данных из формы
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

    // Проверка, существует ли уже покемон с таким именем
    $existingNames = getAllPokemonNames($pdo);

    // Обработка загрузки изображения
    $imageName = null;
    if (isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $files['image']['tmp_name'];
        $imageOriginalName = $files['image']['name'];
        $imageExtension = pathinfo($imageOriginalName, PATHINFO_EXTENSION);
        $imageName = uniqid() . '.' . $imageExtension;
        $targetPath = __DIR__ . '/../../../public/assets/' . $imageName;

        // Проверка доступности директории для записи
        if (!is_writable(dirname($targetPath))) {
            error_log("Directory not writable: " . dirname($targetPath));
            return ['errors' => ['image' => 'Cannot write to assets directory']];
        }

        // Перемещение изображения в целевую директорию
        if (!move_uploaded_file($imageTmpPath, $targetPath)) {
            error_log("Failed to move image to $targetPath");
            return ['errors' => ['image' => 'Failed to upload image']];
        }
    }

    // Логирование данных перед валидацией
    error_log("Data before validation: " . print_r([
        'name' => $name,
        'types' => $types,
        'generation' => $generation,
        'category' => $category,
        'description' => $description,
        'abilities' => $abilities,
        'imageName' => $imageName
    ], true));

    // Валидация данных покемона
    $errors = validatePokemon($name, $types, $generation, $category, $description, $abilities, $existingNames, $imageName);
    if (!empty($errors)) {
        error_log("Validation errors: " . print_r($errors, true));
        return [
            'errors' => $errors,
            'data' => compact('name', 'generation', 'category', 'description', 'typeIds', 'abilities', 'weaknessIds')
        ];
    }

    try {
        // Вставка данных покемона в базу
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

        // Получение ID вставленного покемона
        $pokemonId = $pdo->lastInsertId();
        error_log("Pokemon created with ID: $pokemonId");

        // Вставка слабостей покемона
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

// Обработка POST-запроса
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = createPokemon($pdo, $_POST, $_FILES);

    if (!empty($result['success'])) {
        $_SESSION['success'] = 'Pokémon created successfully';
        header('Location: /pokemanager/public/index.php?action=home');
        exit;
    }

    $_SESSION['errors'] = $result['errors'] ?? [];
    $_SESSION['old'] = $result['data'] ?? [];
    error_log("Redirecting to create with errors: " . print_r($_SESSION['errors'], true));
    header('Location: /pokemanager/public/index.php?action=create');
    exit;
}

// Подключение шаблона для страницы создания покемона
include __DIR__ . '/../../../templates/admin/create.php';
?>
```

### src/handlers/admin/delete.php
Удаление объектов
```php
<?php

/**
 * Удаляет покемона из базы данных по его ID.
 *
 * @param PDO $pdo Подключение к базе данных.
 * @param int $id Идентификатор покемона, которого нужно удалить.
 * @return void
 */
function deletePokemon(PDO $pdo, int $id): void {
    $stmt = $pdo->prepare("DELETE FROM pokemons WHERE id = ?");
    $stmt->execute([$id]);
}
```

### src/handlers/db.php
Основной файл для работы с базой данных.
```php
<?php

/**
 * Создаёт подключение к базе данных с использованием конфигурации из db.php.
 *
 * @return PDO Возвращает объект PDO при успешном подключении.
 * @throws PDOException Если подключение не удалось, выбрасывается исключение.
 */

$config = require __DIR__ . '/../config/db.php';

try {
    $pdo = new PDO($config['dsn'], $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
} catch (PDOException $e) {
    // Останавливаем выполнение и выводим сообщение об ошибке
    die('DB connection failed: ' . $e->getMessage());
}
```

### src/handlers/helpers.php
Файл для хранения вспомогательных функций
```php
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
```

### templates/admin/create.php 
Шаблон для создания новых объектов администратора.
```html
<form action="/pokemanager/public/index.php?action=create" method="post" enctype="multipart/form-data">
    <div>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($data['name'] ?? '') ?>">
        <?php if (!empty($errors['name'])): ?>
            <p class="error"><?= htmlspecialchars($errors['name']) ?></p>
        <?php endif; ?>
    </div>
    <br>

    <div>
        <label for="type">Type (choose up to 2):</label>
        <select name="type[]" id="type" multiple size="5">
            <?php foreach ($types as $type): ?>
                <option value="<?= $type['id'] ?>"
                        <?= in_array($type['id'], $data['typeIds'] ?? []) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['type'])): ?>
            <p class="error"><?= htmlspecialchars($errors['type']) ?></p>
        <?php endif; ?>
    </div>
    <br>

    <div>
        <label for="generation">Generation:</label>
        <select name="generation" id="generation">
            <option value="">Select generation</option>
            <?php foreach ($generations as $gen): ?>
                <option value="<?= $gen['id'] ?>" <?= ($data['generation'] ?? '') == $gen['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($gen['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['generation'])): ?>
            <p class="error"><?= htmlspecialchars($errors['generation']) ?></p>
        <?php endif; ?>
    </div>
    <br>

    <div>
        <label for="category">Category:</label>
        <input type="text" name="category" id="category" value="<?= htmlspecialchars($data['category'] ?? '') ?>">
        <?php if (!empty($errors['category'])): ?>
            <p class="error"><?= htmlspecialchars($errors['category']) ?></p>
        <?php endif; ?>
    </div>
    <br>

    <div>
        <label for="description">Description:</label>
        <textarea name="description" id="description"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
        <?php if (!empty($errors['description'])): ?>
            <p class="error"><?= htmlspecialchars($errors['description']) ?></p>
        <?php endif; ?>
    </div>
    <br>

    <div>
        <label for="weaknesses">Weaknesses:</label>
        <select name="weaknesses[]" multiple id="weaknesses">
            <?php foreach ($weaknesses as $weakness): ?>
                <option value="<?= $weakness['id'] ?>"
                        <?= in_array($weakness['id'], $data['weaknessIds'] ?? []) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($weakness['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['weaknesses'])): ?>
            <p class="error"><?= htmlspecialchars($errors['weaknesses']) ?></p>
        <?php endif; ?>
    </div>
    <br>

    <div>
        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image">
        <?php if (!empty($errors['image'])): ?>
            <p class="error"><?= htmlspecialchars($errors['image']) ?></p>
        <?php endif; ?>
    </div>
    <br>

    <div id="abilities">
        <label>Abilities:</label><br>
        <?php
        $abilityData = $data['abilities'] ?? [''];
        foreach ($abilityData as $abilityText):
        ?>
            <textarea name="abilities[]" placeholder="Enter ability"><?= htmlspecialchars($abilityText) ?></textarea><br>
        <?php endforeach; ?>
    </div>
    <?php if (!empty($errors['abilities'])): ?>
        <p class="error"><?= htmlspecialchars($errors['abilities']) ?></p>
    <?php endif; ?>

    <button type="button" onclick="addAbility()">Add Ability</button><br><br>
    <button type="submit">Save</button>
</form>
```

### templates/admin/edit.php
Шаблон для редактирования объектов администратора
```html
<form action="/pokemanager/public/?action=edit&id=<?= $pokemon['id'] ?>" method="post">
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($pokemon['name'] ?? '') ?>">
    <?php if (!empty($errors['name'])): ?>
        <p style="color:red"><?= htmlspecialchars($errors['name']) ?></p>
    <?php endif; ?>
    <br>
    <button type="submit">Update</button>
    <a href="index.php" class="cancel-button">Cancel</a>
</form>
```

### templates/everyone/show.php 
Шаблон для отображения информации всем пользователям
```html
<div class="pokemon-view">
    <h2><?= htmlspecialchars($pokemon['name']) ?></h2>

    <?php if (!empty($pokemon['image_url'])): ?>
        <img src="/pokemanager/public/assets/<?= htmlspecialchars(basename($pokemon['image_url'])) ?>" alt="Pokemon Image">
    <?php else: ?>
        <p>Image not found.</p>
    <?php endif; ?>


    <p><strong>Category:</strong><br> <?= htmlspecialchars($category ?? 'Не указана') ?></p>
    <p><strong>Type:</strong><br> <?= !empty($typeNames) ? implode(', ', $typeNames) : 'Не указаны' ?></p>
    <p><strong>Generation:</strong><br> <?= htmlspecialchars($generation ?? 'Не указано') ?></p>
    
    <p><strong>Weaknesses:</strong><br> 
        <?= !empty($weaknessNames) ? implode(', ', $weaknessNames) : 'Не указаны' ?>
    </p>

    <p><strong>Description:</strong><br> <?= nl2br(htmlspecialchars($pokemon['description'])) ?></p>

    <?php if (!empty($abilities)): ?>
        <p><strong>Abilities:</strong></p>
        <ul>
            <?php foreach ($abilities as $ability): ?>
                <li><?= htmlspecialchars($ability) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="actions">
        <a href="/pokemanager/public/?action=edit&id=<?= $pokemon['id'] ?>">Edit</a>
        <a href="/pokemanager/public/?action=delete&id=<?= $pokemon['id'] ?>" onclick="return confirm('Delete Pokemon?')">Delete</a>
    </div>
</div>
```

### templates/everyone/index.php 
Шаблон для отображения главной страницы
```php
<?php if (empty($pokemons)): ?>
    <!-- Если список покемонов пуст, выводим сообщение -->
    <p>Pokemon have gone for a better life.</p>
<?php else: ?>
    <div class="pokemon-grid">
        <?php foreach ($pokemons as $pokemon): ?>
            <div class="pokemon-card">
                <!-- Ссылка на страницу покемона -->
                <a href="/pokemanager/public/?action=show&id=<?= $pokemon['id'] ?>">
                    <img src="/pokemanager/<?= htmlspecialchars($pokemon['image_url']) ?>" alt="<?= htmlspecialchars($pokemon['name']) ?>">
                </a>
                <h3><?= htmlspecialchars($pokemon['name']) ?></h3>
                <p><?= htmlspecialchars($pokemon['type']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($totalPages) && $totalPages > 1): ?>
    <!-- Если есть несколько страниц, отображаем пагинацию -->
    <nav class="pagination">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a href="?page=<?= $p ?>" <?= $p == $page ? 'style="font-weight:bold;"' : '' ?>>
                <?= $p ?>
            </a>
        <?php endfor; ?>
    </nav>
<?php endif; ?>
```

### templates/everyone/layout.php 
Основной шаблон для оформления страниц
```html
<header>
<nav>
    <!-- Навигация по страницам -->
    <a href="/pokemanager/public/index.php"><button class="nav-button">Home</button></a>

    <?php if (!isset($_SESSION['user'])): ?>
        <a href="/pokemanager/public/login.php"><button class="nav-button">Login</button></a>
        <a href="/pokemanager/public/register.php"><button class="nav-button">Register</button></a>
        <a href="/pokemanager/public/?action=create"><button class="nav-button">Add Pokemon</button></a>
    <?php else: ?>
        <span style="color: #8e44ad; padding: 8px 10px;">
            <?= htmlspecialchars($_SESSION['user']['email']) ?>
        </span>
        <a href="/pokemanager/public/logout.php"><button class="nav-button">Logout</button></a>
    <?php endif; ?>
</nav>
    <h1>PokeManager</h1>
</header>

<main>
    <!-- Контент страницы, переданный через переменную $content -->
    <?= $content ?? '' ?>
</main>

<footer>
    <!-- Подвал страницы -->
    <p>&copy; pupupup</p>
</footer>
```

### templates/auth/login.php
Шаблон страницы входа.
```html
<form action="../src/handlers/auth/login.php" method="POST">
    <h2>Login</h2>
    
    <label>Username:</label>
    <input type="text" name="username" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <input type="submit" value="Login">
</form>
<p>Don't have an account? <a href="register.php">Register</a></p>
```

### templates/auth/register.php 
Шаблон страницы регистрации.
```html
<form action="../src/handlers/auth/register.php" method="POST">
    <h2>Register</h2>
    
    <label>Username:</label>
    <input type="text" name="username" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Confirm password:</label>
    <input type="password" name="confirm_password" required>

    <input type="submit" value="Register">
</form>
<p>Already have an account? <a href="login.php">Login</a></p>
```

## Примеры использования проекта
![image](https://github.com/user-attachments/assets/6bca891d-40d6-4fe7-85cc-22bd9d683313)
![image](https://github.com/user-attachments/assets/a032ad77-95fb-40e6-bf55-db02fd0cf882)
![image](https://github.com/user-attachments/assets/b01346a3-98a2-44d7-9e58-9d580c6cdfb7)
![image](https://github.com/user-attachments/assets/baa371b1-5073-4dce-a171-b9a775984bc9)

## Библиография
- Репозиторий курса: https://github.com/MSU-Courses/advanced-web-programming
