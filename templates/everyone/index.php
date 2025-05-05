<?php 

ob_start(); 

?>

<a href="/pokemanager/public/?action=create">...</a>

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

    a[href="/list-of-spells/public/?action=create"] {
        display: inline-block;
        padding: 10px 20px;
        margin: 20px;
        background-color: #3498db;
        color: white;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    a[href="/list-of-spells/public/?action=create"]:hover {
        background-color: #2980b9;
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
    <p>Покемоны ушли за лучшей жизнью.</p>
<?php else: ?>
    <ul>
        <?php foreach ($pokemons as $pokemon): ?>
            <li>
                <a href="/pokemanager/public/?action=show&id=<?= $pokemon['id'] ?>">
                    <?= htmlspecialchars($pokemon['title']) ?>
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