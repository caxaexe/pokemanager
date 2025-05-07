<?php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../src/helpers.php';

/**
 * Получение подключения к базе данных.
 *
 * @var PDO $pdo
 */
$pdo = getPdoConnection();

/**
 * Получение ошибок и старых данных из сессии.
 *
 * @var array $errors
 * @var array $data
 */
$errors = $_SESSION['errors'] ?? [];
$data = $_SESSION['old'] ?? [];
error_log("Errors in template: " . print_r($errors, true));
unset($_SESSION['errors'], $_SESSION['old']);

try {
    /**
     * Загрузка справочных данных для формы: типы, поколения, слабости.
     *
     * @var array $types
     * @var array $generations
     * @var array $weaknesses
     */
    $typeStmt = $pdo->query('SELECT id, name FROM types');
    $types = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

    $generationStmt = $pdo->query('SELECT id, name FROM generations');
    $generations = $generationStmt->fetchAll(PDO::FETCH_ASSOC);

    $weaknessStmt = $pdo->query('SELECT id, name FROM weaknesses');
    $weaknesses = $weaknessStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error in template: " . $e->getMessage());
    $errors['database'] = 'Failed to load form data: ' . $e->getMessage();
    $types = $generations = $weaknesses = [];
}

ob_start();
?>

<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f2f7fa;
        color: #333;
        padding: 20px;
    }
    h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 20px;
    }
    form {
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 0 auto;
    }
    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
        color: #34495e;
    }
    input[type="text"],
    textarea,
    select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-sizing: border-box;
        font-size: 14px;
        background-color: #fafafa;
    }
    textarea {
        min-height: 80px;
        resize: vertical;
    }
    input[type="file"] {
        margin-top: 8px;
    }
    .error {
        color: #e74c3c;
        font-size: 0.9em;
        margin-top: 5px;
    }
    button[type="submit"],
    button[type="button"] {
        margin-top: 20px;
        background-color: rgb(218, 197, 38);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: rgb(204, 181, 6);
    }
    select[multiple] {
        height: auto;
        min-height: 100px;
    }
    #abilities textarea {
        margin-top: 8px;
    }
</style>

<h2>Create your own Pokemon</h2>

<?php if (isset($_SESSION['success'])): ?>
    <p style="color: green;"><?= htmlspecialchars($_SESSION['success']) ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($errors['database'])): ?>
    <p class="error"><?= htmlspecialchars($errors['database']) ?></p>
<?php endif; ?>

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

<script>
    /**
     * Добавляет новое поле textarea для способностей.
     */
    function addAbility() {
        const newAbility = document.createElement('textarea');
        newAbility.name = 'abilities[]';
        newAbility.placeholder = 'Enter ability';
        document.getElementById('abilities').appendChild(newAbility);
        document.getElementById('abilities').appendChild(document.createElement('br'));
    }

    /**
     * Ограничивает выбор типов до двух.
     */
    const typeSelect = document.getElementById('type');
    typeSelect.addEventListener('change', function () {
        const selectedOptions = Array.from(this.selectedOptions);
        if (selectedOptions.length > 2) {
            selectedOptions[selectedOptions.length - 1].selected = false;
            alert('You can select up to 2 types only.');
        }
    });
</script>

<?php
/**
 * Буферизированный вывод страницы с формой помещается в переменную $content,
 * после чего подключается основной layout.
 */
$content = ob_get_clean();
include __DIR__ . '/../everyone/layout.php';
?>