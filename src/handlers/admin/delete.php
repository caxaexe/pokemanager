<?php

function deletePokemon(PDO $pdo, $id) {
    // Подготовка SQL-запроса для удаления заклинания по ID
    $stmt = $pdo->prepare("DELETE FROM pokemons WHERE id = ?");
    $stmt->execute([$id]);
}