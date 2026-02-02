<?php
/**
 * Admin Dashboard - Stand Automóvel
 * Visão Geral do Negócio
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Dashboard';

// Estatísticas gerais
try {
    $stats = dbQuery("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN vendido = 0 THEN 1 ELSE 0 END) as disponiveis,
        SUM(CASE WHEN vendido = 1 THEN 1 ELSE 0 END) as vendidos,
        SUM(CASE WHEN vendido = 0 AND (reservado = 0 OR reservado IS NULL) THEN 1 ELSE 0 END) as livres,
        SUM(CASE WHEN vendido = 0 AND reservado = 1 THEN 1 ELSE 0 END) as reservados,
        SUM(CASE WHEN vendido = 0 THEN preco ELSE 0 END) as valor_stock,
        SUM(CASE WHEN vendido = 1 THEN preco ELSE 0 END) as valor_vendido,
        SUM(CASE WHEN vendido = 1 AND MONTH(data_venda) = MONTH(CURRENT_DATE()) AND YEAR(data_venda) = YEAR(CURRENT_DATE()) THEN 1 ELSE 0 END) as vendas_mes,
        SUM(CASE WHEN vendido = 1 AND MONTH(data_venda) = MONTH(CURRENT_DATE()) AND YEAR(data_venda) = YEAR(CURRENT_DATE()) THEN preco ELSE 0 END) as faturado_mes
    FROM cars")[0];
    
    // Atividade recente
    $recentActivity = dbQuery("
        SELECT 'venda' as tipo, CONCAT(marca, ' ', modelo) as viatura, data_venda as data, id
        FROM cars 
        WHERE vendido = 1 AND data_venda IS NOT NULL
        UNION ALL
        SELECT 'adicao' as tipo, CONCAT(marca, ' ', modelo) as viatura, data_registo as data, id
        FROM cars 
        WHERE data_registo >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        UNION ALL
        SELECT 'reserva' as tipo, CONCAT(marca, ' ', modelo) as viatura, data_reserva as data, id
        FROM cars 
        WHERE reservado = 1 AND data_reserva IS NOT NULL
        ORDER BY data DESC 
        LIMIT 10
    ");
    
} catch (Exception $e) {
    $stats = ['total' => 0, 'disponiveis' => 0, 'vendidos' => 0, 'livres' => 0, 'reservados' => 0, 'valor_stock' => 0, 'valor_vendido' => 0, 'vendas_mes' => 0, 'faturado_mes' => 0];
    $recentActivity = [];
}

// Mensagens
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Visão geral do negócio</p>
</div>

<!-- Mensagens -->
<?php if ($success === 'added'): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        Viatura adicionada com sucesso!
    </div>
<?php elseif ($success === 'updated'): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        Viatura atualizada com sucesso!
    </div>
<?php elseif ($success === 'deleted'): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        Viatura eliminada com sucesso!
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle me-2"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Stats Row 1 - Core Metrics -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="bi bi-car-front"></i>
            </div>
            <div>
                <div class="stat-number"><?php echo $stats['disponiveis']; ?></div>
                <div class="stat-label">Viaturas Disponíveis</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div>
                <div class="stat-number"><?php echo $stats['vendidos']; ?></div>
                <div class="stat-label">Viaturas Vendidas</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="bi bi-currency-euro"></i>
            </div>
            <div>
                <div class="stat-number"><?php echo number_format($stats['valor_stock'], 0, ',', '.'); ?>€</div>
                <div class="stat-label">Valor em Stock</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #7c3aed;">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div>
                <div class="stat-number"><?php echo $stats['vendas_mes']; ?></div>
                <div class="stat-label">Vendas Este Mês</div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row 2 - Business Insights -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="stat-icon" style="background: rgba(255,255,255,0.2); color: white;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div>
                <div class="stat-number" style="color: white;"><?php echo number_format($stats['faturado_mes'], 0, ',', '.'); ?>€</div>
                <div class="stat-label" style="color: rgba(255,255,255,0.8);">Faturado Este Mês</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #7c3aed;">
                <i class="bi bi-percent"></i>
            </div>
            <div>
                <?php 
                $taxaConversao = $stats['total'] > 0 ? round(($stats['vendidos'] / $stats['total']) * 100) : 0;
                ?>
                <div class="stat-number"><?php echo $taxaConversao; ?>%</div>
                <div class="stat-label">Taxa de Conversão</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background: rgba(236, 72, 153, 0.1); color: #db2777;">
                <i class="bi bi-tag"></i>
            </div>
            <div>
                <?php 
                $precoMedio = $stats['disponiveis'] > 0 ? $stats['valor_stock'] / $stats['disponiveis'] : 0;
                ?>
                <div class="stat-number"><?php echo number_format($precoMedio, 0, ',', '.'); ?>€</div>
                <div class="stat-label">Preço Médio Stock</div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row">
    <!-- Recent Activity -->
    <div class="col-lg-8 mb-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <i class="bi bi-clock-history"></i>
                    Atividade Recente
                </h2>
            </div>
            
            <?php if (empty($recentActivity)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #e5e7eb;"></i>
                    <p class="text-muted mt-3">Ainda não há atividade registada.</p>
                </div>
            <?php else: ?>
                <div class="activity-timeline">
                    <?php foreach ($recentActivity as $activity): ?>
                        <?php
                        $icons = [
                            'venda' => ['icon' => 'bi-check-circle-fill', 'color' => '#22c55e', 'bg' => 'rgba(34, 197, 94, 0.1)', 'label' => 'Vendido'],
                            'adicao' => ['icon' => 'bi-plus-circle-fill', 'color' => '#3b82f6', 'bg' => 'rgba(59, 130, 246, 0.1)', 'label' => 'Adicionado'],
                            'reserva' => ['icon' => 'bi-bookmark-fill', 'color' => '#ca8a04', 'bg' => 'rgba(234, 179, 8, 0.1)', 'label' => 'Reservado']
                        ];
                        $config = $icons[$activity['tipo']];
                        ?>
                        <div class="activity-item">
                            <div class="activity-icon" style="background: <?php echo $config['bg']; ?>; color: <?php echo $config['color']; ?>;">
                                <i class="bi <?php echo $config['icon']; ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">
                                    <span class="activity-badge" style="background: <?php echo $config['bg']; ?>; color: <?php echo $config['color']; ?>;">
                                        <?php echo $config['label']; ?>
                                    </span>
                                    <strong><?php echo htmlspecialchars($activity['viatura']); ?></strong>
                                </div>
                                <div class="activity-time">
                                    <i class="bi bi-clock"></i>
                                    <?php 
                                    $timestamp = strtotime($activity['data']);
                                    $diff = time() - $timestamp;
                                    if ($diff < 3600) {
                                        echo 'Há ' . round($diff / 60) . ' minutos';
                                    } elseif ($diff < 86400) {
                                        echo 'Há ' . round($diff / 3600) . ' horas';
                                    } else {
                                        echo date('d/m/Y H:i', $timestamp);
                                    }
                                    ?>
                                </div>
                            </div>
                            <a href="viatura.php?id=<?php echo $activity['id']; ?>" class="activity-link">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h2 class="admin-card-title">
                    <i class="bi bi-lightning-fill"></i>
                    Ações Rápidas
                </h2>
            </div>
            
            <div class="quick-actions">
                <a href="viatura.php" class="quick-action-card" style="border-left: 4px solid var(--primary-color);">
                    <div class="quick-action-icon" style="background: rgba(242, 101, 34, 0.1); color: var(--primary-color);">
                        <i class="bi bi-plus-circle-fill"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Adicionar Viatura</div>
                        <div class="quick-action-desc">Registar nova viatura no sistema</div>
                    </div>
                    <i class="bi bi-chevron-right quick-action-arrow"></i>
                </a>
                
                <a href="disponiveis.php" class="quick-action-card" style="border-left: 4px solid #3b82f6;">
                    <div class="quick-action-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <i class="bi bi-car-front"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Ver Disponíveis</div>
                        <div class="quick-action-desc"><?php echo $stats['disponiveis']; ?> viaturas em stock</div>
                    </div>
                    <i class="bi bi-chevron-right quick-action-arrow"></i>
                </a>
                
                <a href="vendidos.php" class="quick-action-card" style="border-left: 4px solid #22c55e;">
                    <div class="quick-action-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="quick-action-content">
                        <div class="quick-action-title">Ver Vendidas</div>
                        <div class="quick-action-desc"><?php echo $stats['vendidos']; ?> viaturas vendidas</div>
                    </div>
                    <i class="bi bi-chevron-right quick-action-arrow"></i>
                </a>
            </div>
        </div>
        
        <!-- Stock Status Mini Card -->
        <div class="admin-card mt-3">
            <div class="admin-card-header" style="margin-bottom: 1rem; padding-bottom: 0.75rem;">
                <h2 class="admin-card-title" style="font-size: 1rem;">
                    <i class="bi bi-pie-chart-fill"></i>
                    Estado do Stock
                </h2>
            </div>
            
            <div style="padding: 0 0.5rem;">
                <div class="stock-status-item">
                    <div class="stock-status-label">
                        <span class="stock-status-dot" style="background: #3b82f6;"></span>
                        Livres
                    </div>
                    <div class="stock-status-value"><?php echo $stats['livres']; ?></div>
                </div>
                <div class="stock-status-item">
                    <div class="stock-status-label">
                        <span class="stock-status-dot" style="background: #ca8a04;"></span>
                        Reservados
                    </div>
                    <div class="stock-status-value"><?php echo $stats['reservados']; ?></div>
                </div>
                <div class="stock-status-item" style="border-bottom: none; padding-bottom: 0;">
                    <div class="stock-status-label">
                        <span class="stock-status-dot" style="background: #22c55e;"></span>
                        Vendidos
                    </div>
                    <div class="stock-status-value"><?php echo $stats['vendidos']; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Activity Timeline */
.activity-timeline {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--bg-light);
    border-radius: 12px;
    transition: all 0.2s ease;
}

.activity-item:hover {
    background: rgba(242, 101, 34, 0.05);
    transform: translateX(5px);
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.activity-content {
    flex-grow: 1;
}

.activity-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.activity-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.activity-time {
    font-size: 0.85rem;
    color: var(--text-gray);
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.activity-link {
    color: var(--text-gray);
    font-size: 1.25rem;
    transition: all 0.2s ease;
}

.activity-link:hover {
    color: var(--primary-color);
    transform: translateX(3px);
}

/* Quick Actions */
.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.quick-action-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s ease;
}

.quick-action-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.quick-action-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.quick-action-content {
    flex-grow: 1;
}

.quick-action-title {
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.15rem;
}

.quick-action-desc {
    font-size: 0.85rem;
    color: var(--text-gray);
}

.quick-action-arrow {
    color: var(--text-gray);
    font-size: 1.25rem;
    transition: all 0.2s ease;
}

.quick-action-card:hover .quick-action-arrow {
    color: var(--primary-color);
    transform: translateX(3px);
}

/* Stock Status */
.stock-status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.stock-status-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: var(--text-dark);
}

.stock-status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.stock-status-value {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--text-dark);
}
</style>

<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        const url = new URL(window.location);
        if (url.searchParams.has('success') || url.searchParams.has('error')) {
            url.searchParams.delete('success');
            url.searchParams.delete('error');
            window.history.replaceState({}, '', url);
        }

        setTimeout(function() {
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 600);
            });
        }, 5000);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
