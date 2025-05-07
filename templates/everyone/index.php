<?php ob_start(); ?>

<?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
    <!-- Если пользователь авторизован как администратор, отображаем кнопку для создания покемона -->
    <a href="/pokemanager/public/?action=create">
        <button class="nav-button">Create pokemon</button>
    </a>
<?php endif; ?>


<style>
    .pokemon-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin: 30px 0;
        padding: 0 10px;
    }

    .pokemon-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .pokemon-card:hover {
        transform: translateY(-5px);
    }

    .pokemon-card img {
        width: 100%;
        height: 180px;
        object-fit: contain;
        background-color: #f9f9f9;
        border-bottom: 1px solid #eee;
    }

    .pokemon-card h3 {
        margin: 10px 0 5px;
        font-size: 1.3em;
        color: #333;
    }

    .pokemon-card p {
        margin-bottom: 15px;
        color: #777;
    }

    .pagination {
        text-align: center;
        margin: 30px 0;
    }

    .pagination a {
        margin: 0 5px;
        padding: 8px 12px;
        background-color: #ecf0f1;
        color: #3498db;
        border-radius: 5px;
        transition: background-color 0.3s ease;
        text-decoration: none;
    }

    .pagination a:hover {
        background-color: #3498db;
        color: white;
    }

    .pagination a[style*="font-weight:bold;"] {
        background-color: #3498db;
        color: white;
        font-weight: bold;
    }


</style>

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

<?php
/**
 * Собираем содержимое страницы и подключаем основной layout.
 */
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
