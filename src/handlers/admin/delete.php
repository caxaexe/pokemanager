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
