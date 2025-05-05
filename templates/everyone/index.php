<?php 

ob_start(); 

?>

<a href="/pokemanager/public/?action=create">...</a>
<?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
    <a href="/pokemanager/public/?action=create">
        <button class="nav-button">Create pokemon</button>
    </a>
<?php endif; ?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        color: #333;
    }

    h1, h2, h3, p {
        margin: 0;
        padding: 0;
    }

    a {
        text-decoration: none;
        color: #3498db;
    }

    a:hover {
        color: #2c3e50;
    }


    ul {
        list-style: none;
        padding-left: 0;
    }

    li {
        margin: 10px 0;
        padding: 10px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    li a {
        font-size: 18px;
        font-weight: bold;
        color: #3498db;
    }

    li a:hover {
        color: #2980b9;
    }

    p {
        font-size: 18px;
        color: #7f8c8d;
        text-align: center;
        margin-top: 20px;
    }

    .pagination {
        text-align: center;
        margin: 20px 0;
    }

    .pagination a {
        margin: 0 5px;
        padding: 8px 12px;
        background-color: #ecf0f1;
        color: #3498db;
        border-radius: 5px;
        transition: background-color 0.3s ease;
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
    <p>Pokemon have gone for a better life.</p>
<?php else: ?>
    <ul>
        <?php foreach ($pokemons as $pokemon): ?>
            <li>
                <a href="/pokemanager/public/?action=show&id=<?= $pokemon['id'] ?>">
                    <?= htmlspecialchars($pokemon['name']) ?>
                </a><br>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($totalPages) && $totalPages > 1): ?>
    <nav class="pagination">
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a href="?page=<?= $p ?>" <?= $p == $page ? 'style="font-weight:bold;"' : '' ?>>
                <?= $p ?>
            </a>
        <?php endfor; ?>
    </nav>
<?php endif; ?>

<?php

$content = ob_get_clean(); 
include __DIR__ . '/layout.php'; 

?>