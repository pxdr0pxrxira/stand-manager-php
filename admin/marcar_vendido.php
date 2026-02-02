<?php
/**
 * Endpoint para marcar/desmarcar carro como vendido
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';

requireAuth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

$carId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$vendido = isset($_POST['vendido']) ? (int)$_POST['vendido'] : 0;

if ($carId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

try {
    if ($vendido) {
        // Marcar como vendido
        dbExecute(
            "UPDATE cars SET vendido = 1, data_venda = NOW() WHERE id = :id",
            [':id' => $carId]
        );
    } else {
        // Desmarcar como vendido
        dbExecute(
            "UPDATE cars SET vendido = 0, data_venda = NULL WHERE id = :id",
            [':id' => $carId]
        );
    }
    
    echo json_encode(['success' => true, 'vendido' => $vendido]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()]);
}
