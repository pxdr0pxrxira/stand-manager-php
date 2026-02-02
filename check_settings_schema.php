<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("DESCRIBE settings");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo implode(", ", $columns);
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
