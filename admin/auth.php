<?php
/**
 * ============================================
 * Admin - Verificação de Autenticação
 * ============================================
 * Incluir este ficheiro em todas as páginas do admin
 * Redireciona para login se não autenticado
 */

// Iniciar sessão se ainda não iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica se o utilizador está autenticado
 * Redireciona para login se não estiver
 * 
 * @return void
 */
function requireAuth(): void {
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
        // Guardar URL para redirect após login
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        
        header('Location: login.php');
        exit;
    }
    
    // Regenerar ID da sessão periodicamente por segurança
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutos
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

/**
 * Obter dados do admin autenticado
 * 
 * @return array|null Dados do admin ou null
 */
function getCurrentAdmin(): ?array {
    if (!isset($_SESSION['admin_id'])) {
        return null;
    }
    
    return [
        'id' => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username']
    ];
}

/**
 * Verificar se está autenticado (sem redirect)
 * 
 * @return bool
 */
function isAuthenticated(): bool {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}
