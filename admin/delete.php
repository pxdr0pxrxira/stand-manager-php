<?php
/**
 * ============================================
 * Admin - Eliminar Carro
 * ============================================
 * Remove viatura da base de dados
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';

requireAuth();

// Apenas aceitar POST para segurança
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$carId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($carId <= 0) {
    $_SESSION['error_message'] = 'ID de viatura inválido.';
    header('Location: dashboard.php');
    exit;
}

try {
    // Buscar dados do carro para apagar a imagem
    $cars = dbQuery("SELECT imagem_path FROM cars WHERE id = :id LIMIT 1", [':id' => $carId]);
    
    if (empty($cars)) {
        $_SESSION['error_message'] = 'Viatura não encontrada.';
        header('Location: dashboard.php');
        exit;
    }
    
    $car = $cars[0];
    
    // Eliminar da base de dados
    $affected = dbExecute("DELETE FROM cars WHERE id = :id", [':id' => $carId]);
    
    if ($affected > 0) {
        // Apagar imagem associada
        if (!empty($car['imagem_path'])) {
            $imagePath = __DIR__ . '/../uploads/' . $car['imagem_path'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $_SESSION['success_message'] = 'Viatura eliminada com sucesso!';
    } else {
        $_SESSION['error_message'] = 'Não foi possível eliminar a viatura.';
    }
    
} catch (Exception $e) {
    error_log('Erro ao eliminar carro: ' . $e->getMessage());
    $_SESSION['error_message'] = 'Ocorreu um erro ao eliminar. Tente novamente.';
}

header('Location: dashboard.php');
exit;
