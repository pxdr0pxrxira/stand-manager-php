<?php
require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();
$stmt = $pdo->query("SELECT id, marca, modelo, versao, descricao FROM cars");
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($cars, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
