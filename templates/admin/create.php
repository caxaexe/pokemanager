<?php

require_once __DIR__ . '/../../config/auth.php';
// requireAdmin();


ob_start();


$typeStmt = $pdo->query('SELECT id, name FROM types');
$types = $typeStmt->fetchAll(PDO::FETCH_ASSOC);

$generationStmt = $pdo->query('SELECT id, name FROM generations');
$generations = $generationStmt->fetchAll(PDO::FETCH_ASSOC);

$weaknessStmt = $pdo->query('SELECT id, name FROM weaknesses');
$weaknesses = $weaknessStmt->fetchAll(PDO::FETCH_ASSOC);

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
        background-color: #3498db;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #2980b9;
    }

    select[multiple] {
        height: auto;
        min-height: 100px;
    }

    #abilities textarea {
        margin-top: 8px;
    }
</style>


<h2>Create your own pokemon</h2>

<form action="/pokemanager/public/?action=create" method="post" enctype="multipart/form-data">
    <div>
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($data['name'] ?? '') ?>">
    <?php if (!empty($errors['name'])): ?>
        <p style="color:red"><?= htmlspecialchars($errors['name']) ?></p>
    <?php endif; ?>
    </div>
    <br>

    <div>
    <label for="type">Type(choose up to 2):</label>
    <select name="type[]" id="type" multiple size="5">
        <?php
        $typeStmt = $pdo->query("SELECT id, name FROM types");
        $allTypes = $typeStmt->fetchAll(PDO::FETCH_ASSOC);
        $selectedTypes = $data['type'] ?? [];
        ?>

        <?php foreach ($allTypes as $type): ?>
            <option value="<?= $type['id'] ?>"
                <?= in_array($type['id'], $selectedTypes) ? 'selected' : '' ?>>
                <?= htmlspecialchars($type['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if (isset($errors['type'])): ?>
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
        <p style="color:red"><?= htmlspecialchars($errors['generation']) ?></p>
    <?php endif; ?>
    </div>
    <br>


    <div>
    <label for="category">Category:</label>
    <input type="text" name="category" id="category" value="<?= htmlspecialchars($data['category'] ?? '') ?>">
    <?php if (!empty($errors['category'])): ?>
        <p style="color:red"><?= htmlspecialchars($errors['category']) ?></p>
    <?php endif; ?>
    <br>


    <div>
    <label for="description">Description:</label>
    <textarea name="description" id="description"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
    <?php if (!empty($errors['description'])): ?>
        <p style="color:red"><?= htmlspecialchars($errors['description']) ?></p>
    <?php endif; ?>
    </div>
    <br>


    <div>
    <label for="weaknesses">Weaknesses:</label>
    <select name="weaknesses[]" id="weaknesses" multiple size="5">
        <?php
        $weaknessStmt = $pdo->query("SELECT id, name FROM weaknesses");
        $allWeaknesses = $weaknessStmt->fetchAll(PDO::FETCH_ASSOC);
        $selectedWeaknesses = $data['weaknesses'] ?? [];
        ?>

        <?php foreach ($allWeaknesses as $weakness): ?>
            <option value="<?= $weakness['id'] ?>"
                <?= in_array($weakness['id'], $selectedWeaknesses) ? 'selected' : '' ?>>
                <?= htmlspecialchars($weakness['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if (isset($errors['weaknesses'])): ?>
        <p class="error"><?= htmlspecialchars($errors['weaknesses']) ?></p>
    <?php endif; ?>
    </div>
    <br>


    <div>
    <label for="image">Pokemon Image:</label>
    <input type="file" name="image" id="image" accept="image/*">

    <?php if (!empty($errors['image'])): ?>
    <p style="color:red"><?= htmlspecialchars($errors['image']) ?></p>
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
        <p style="color:red"><?= htmlspecialchars($errors['abilities']) ?></p>
    <?php endif; ?>

    <button type="button" onclick="addAbility()">Enter ability</button><br><br>

    <button type="submit">Save</button>
</form>

<script>
    function addAbility() {
        const newAbility = document.createElement('textarea');
        newAbility.name = 'abilities[]';
        newAbility.placeholder = 'Enter ability';
        document.getElementById('abilities').appendChild(newAbility);
        document.getElementById('abilities').appendChild(document.createElement('br'));
    }

    const typeSelect = document.getElementById('types');

    typeSelect.addEventListener('change', function () {
        const selectedOptions = Array.from(this.selectedOptions);
        if (selectedOptions.length > 2) {
            selectedOptions[selectedOptions.length - 1].selected = false;
            alert('You can select up to 2 types only.');
        }
    });
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../everyone/layout.php';
?>