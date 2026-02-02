<?php
/**
 * Endpoint para marcar/desmarcar carro como reservado
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
$reservado = isset($_POST['reservado']) ? (int)$_POST['reservado'] : 0;

if ($carId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

try {
    if ($reservado) {
        // Marcar como reservado
        dbExecute(
            "UPDATE cars SET reservado = 1, data_reserva = NOW() WHERE id = :id",
            [':id' => $carId]
        );
    } else {
        // Desmarcar reserva
        dbExecute(
            "UPDATE cars SET reservado = 0, data_reserva = NULL WHERE id = :id",
            [':id' => $carId]
        );
    }
    
    echo json_encode(['success' => true, 'reservado' => $reservado]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar: ' . $e->getMessage()]);
}
