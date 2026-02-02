<?php
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    
    // Verificar se a setting já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = ?");
    $stmt->execute(['semi_new_max_km']);
    
    if ($stmt->fetchColumn() == 0) {
        // Inserir nova setting
        $sql = "INSERT INTO settings (setting_key, setting_value, setting_label, setting_type) 
                VALUES ('semi_new_max_km', '10000', 'Máximo de Quilómetros para Semi-novo', 'number')";
        $pdo->exec($sql);
        echo "Setting 'semi_new_max_km' adicionada com sucesso.\n";
    } else {
        echo "Setting 'semi_new_max_km' já existe.\n";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>
