<?php
/**
 * ============================================
 * Configuração da Base de Dados
 * ============================================
 * Ficheiro de conexão PDO com MySQL
 * Usa prepared statements para segurança
 */

// Configurações da base de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'stand_automovel');
define('DB_USER', 'root');
define('DB_PASS', ''); // Alterar em produção!
define('DB_CHARSET', 'utf8mb4');

/**
 * Estabelece conexão com a base de dados
 * 
 * @return PDO Objeto de conexão PDO
 * @throws PDOException Em caso de erro de conexão
 */
function getDBConnection(): PDO {
    static $pdo = null;
    
    // Retorna conexão existente se já criada (Singleton)
    if ($pdo !== null) {
        return $pdo;
    }
    
    // DSN (Data Source Name) para MySQL
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        DB_HOST,
        DB_NAME,
        DB_CHARSET
    );
    
    // Opções de configuração do PDO
    $options = [
        // Lançar exceções em caso de erro
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Retornar resultados como arrays associativos
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        // Desativar prepared statements emulados (mais seguro)
        PDO::ATTR_EMULATE_PREPARES => false,
        // Manter conexão persistente (melhor performance)
        PDO::ATTR_PERSISTENT => true
    ];
    
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Em produção, não mostrar detalhes do erro
        error_log('Erro de conexão à BD: ' . $e->getMessage());
        die('Erro de ligação à base de dados. Por favor, tente mais tarde.');
    }
}

/**
 * Função auxiliar para executar queries SELECT
 * 
 * @param string $sql Query SQL com placeholders
 * @param array $params Parâmetros para bind
 * @return array Resultados da query
 */
function dbQuery(string $sql, array $params = []): array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Função auxiliar para executar queries INSERT/UPDATE/DELETE
 * 
 * @param string $sql Query SQL com placeholders
 * @param array $params Parâmetros para bind
 * @return int Número de linhas afetadas
 */
function dbExecute(string $sql, array $params = []): int {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/**
 * Função auxiliar para obter o último ID inserido
 * 
 * @return string Último ID inserido
 */
function dbLastInsertId(): string {
    return getDBConnection()->lastInsertId();
}
