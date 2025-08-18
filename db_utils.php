<?php
/**
 * Проверяет, есть ли все указанные таблицы в базе
 */
function dbHasTables(PDO $pdo, array $tables): bool {
    $placeholders = str_repeat('?,', count($tables) - 1) . '?';
    $sql = "SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
              AND table_name IN ($placeholders)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($tables);
    $foundTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    return count($foundTables) === count($tables);
}