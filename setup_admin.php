<?php
/**
 * Script temporário para criar/resetar admin
 * APAGAR DEPOIS DE USAR!
 */

require_once __DIR__ . '/config/database.php';

$username = 'admin';
$password = 'admin123';

// Gerar hash correto
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>A criar admin...</h2>";
echo "<p>Username: <strong>$username</strong></p>";
echo "<p>Password: <strong>$password</strong></p>";
echo "<p>Hash gerado: <code>$hash</code></p>";

try {
    // Apagar admin existente
    dbExecute("DELETE FROM admins WHERE username = :username", [':username' => $username]);
    
    // Inserir novo admin com hash correto
    dbExecute(
        "INSERT INTO admins (username, password_hash) VALUES (:username, :hash)",
        [':username' => $username, ':hash' => $hash]
    );
    
    echo "<p style='color: green; font-size: 1.5em;'>✅ Admin criado com sucesso!</p>";
    echo "<p><a href='admin/login.php'>Ir para Login</a></p>";
    echo "<p style='color: red;'><strong>⚠️ APAGAR este ficheiro (setup_admin.php) depois de usar!</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro: " . htmlspecialchars($e->getMessage()) . "</p>";
}
