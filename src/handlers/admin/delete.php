<?php

function deletePokemon(PDO $pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM pokemons WHERE id = ?");
    $stmt->execute([$id]);
}