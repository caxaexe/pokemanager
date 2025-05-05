<?php

/**
 * Удаляет заклинание из базы данных по его идентификатору.
 *
 * Эта функция принимает ID заклинания и выполняет SQL-запрос для его удаления из таблицы "spells".
 *
 * @param PDO $pdo Объект подключения к базе данных.
 * @param int $id Идентификатор заклинания, которое нужно удалить.
 *
 * @return void
 */
function deleteSpell(PDO $pdo, $id) {
    // Подготовка SQL-запроса для удаления заклинания по ID
    $stmt = $pdo->prepare("DELETE FROM spells WHERE id = ?");
    $stmt->execute([$id]);
}