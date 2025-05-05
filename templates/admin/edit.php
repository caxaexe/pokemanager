<?php
/**
 * Форма редактирования заклинания.
 * Загружает список категорий и тегов из базы данных, отображает текущие данные заклинания,
 * позволяет изменить заголовок, категорию, описание, теги и шаги.
 */

ob_start();

/** @var PDO $pdo Подключение к базе данных. */

// Получение списка категорий из базы данных
$categoryStmt = $pdo->query('SELECT id, name FROM categories');
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// Преобразование тегов заклинания в массив ID (если они сохранены в виде строки)
$selectedTags = is_array($spell['tags']) ? $spell['tags'] : (empty($spell['tags']) ? [] : explode(',', $spell['tags']));
?>

<style>
    h2 {
        font-size: 2rem;
        color: #5d3a9b;
        margin-bottom: 20px;
        text-align: center; 
    }

    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        width: 60%;
        margin: 0 auto;
    }

    label {
        display: block;
        font-size: 1rem;
        margin-bottom: 8px;
    }

    input, select, textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
        box-sizing: border-box;
    }

    input:focus, select:focus, textarea:focus {
        border-color: #5d3a9b;
        outline: none;
    }

    button {
        background-color: #5d3a9b;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #4b2c81;
    }

    .errors ul {
        padding-left: 20px;
        color: red;
    }

    .errors li {
        font-size: 0.9rem;
    }

    .error {
        color: red;
        font-size: 0.9rem;
    }

    #steps textarea {
        margin-bottom: 10px;
        font-size: 1rem;
    }

    #steps button {
        margin-top: 10px;
        background-color: #5d3a9b;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    #steps button:hover {
        background-color: #4b2c81;
    }
</style>

<h2>Редактировать заклинание</h2>

<?php if (!empty($errors)): ?>
    <div class="errors">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="/list-of-spells/public/?action=edit&id=<?= $spell['id'] ?>" method="post">
    <label for="title">Название:</label>
    <input type="text" name="title" id="title" value="<?= htmlspecialchars($spell['title'] ?? '') ?>">
    <?php if (!empty($errors['title'])): ?>
        <p style="color:red"><?= htmlspecialchars($errors['title']) ?></p>
    <?php endif; ?>
    <br>

    <label for="category">Категория:</label>
    <select name="category" id="category">
        <option value="">Выберите категорию</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($spell['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (!empty($errors['category'])): ?>
        <p style="color:red"><?= htmlspecialchars($errors['category']) ?></p>
    <?php endif; ?>
    <br>

    <label for="description">Описание:</label>
    <textarea name="description" id="description"><?= htmlspecialchars($spell['description'] ?? '') ?></textarea>
    <?php if (!empty($errors['description'])): ?>
        <p style="color:red"><?= htmlspecialchars($errors['description']) ?></p>
    <?php endif; ?>
    <br>

    <div>
        <label for="tags">Теги:</label>
        <select name="tags[]" id="tags" multiple>
            <?php
            /**
             * Получение всех тегов из базы данных.
             * @var array[] $allTags Массив тегов в формате [['id' => int, 'name' => string], ...]
             */
            $tagStmt = $pdo->query("SELECT id, name FROM tags");
            $allTags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php foreach ($allTags as $tag): ?>
                <option value="<?= $tag['id'] ?>"
                    <?= in_array($tag['id'], $selectedTags) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tag['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <?php if (isset($errors['tags'])): ?>
            <p class="error"><?= htmlspecialchars($errors['tags']) ?></p>
        <?php endif; ?>
    </div>
    <br>

    <label>Шаги выполнения:</label><br>
    <div id="steps">
        <?php
        $stepData = $spell['steps'] ?? [''];
        foreach ($stepData as $stepText):
        ?>
            <textarea name="steps[]" placeholder="Введите шаг выполнения заклинания"><?= htmlspecialchars($stepText) ?></textarea><br>
        <?php endforeach; ?>
    </div>
    <?php if (!empty($errors['steps'])): ?>
        <p style="color:red"><?= htmlspecialchars($errors['steps']) ?></p>
    <?php endif; ?>

    <button type="button" onclick="addStep()">Добавить шаг</button><br><br>

    <button type="submit">Сохранить изменения</button>
</form>

<script>
    function addStep() {
        const newStep = document.createElement('textarea');
        newStep.name = 'steps[]';
        newStep.placeholder = 'Введите шаг выполнения заклинания';
        document.getElementById('steps').appendChild(newStep);
        document.getElementById('steps').appendChild(document.createElement('br'));
    }
</script>

<?php

// Завершаем буферизацию и включаем layout
$content = ob_get_clean();
include __DIR__ . '/../layout.php';

?>