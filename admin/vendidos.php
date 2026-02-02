<?php
/**
 * Admin - Viaturas Vendidas
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Viaturas Vendidas';

// Buscar carros vendidos (vendido = 1)
try {
    $cars = dbQuery("SELECT * FROM cars WHERE vendido = 1 ORDER BY data_venda DESC");
    
    // Estatísticas de vendidos
    $stats = dbQuery("SELECT 
        COUNT(*) as total,
        SUM(preco) as valor_total,
        AVG(preco) as preco_medio,
        SUM(CASE WHEN MONTH(data_venda) = MONTH(CURRENT_DATE()) AND YEAR(data_venda) = YEAR(CURRENT_DATE()) THEN 1 ELSE 0 END) as vendas_mes,
        SUM(CASE WHEN MONTH(data_venda) = MONTH(CURRENT_DATE()) AND YEAR(data_venda) = YEAR(CURRENT_DATE()) THEN preco ELSE 0 END) as faturado_mes
    FROM cars WHERE vendido = 1")[0];
    
} catch (Exception $e) {
    $cars = [];
    $stats = ['total' => 0, 'valor_total' => 0, 'preco_medio' => 0, 'vendas_mes' => 0, 'faturado_mes' => 0];
}

// Mensagens
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">Viaturas Vendidas</h1>
    <p class="page-subtitle">Histórico de vendas</p>
</div>

<!-- Mensagens -->
<?php if ($success === 'deleted'): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        Viatura eliminada com sucesso!
    </div>
<?php elseif ($success === 'restored'): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle me-2"></i>
        Viatura reposta como disponível!
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle me-2"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Stats Row -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div>
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total Vendidos</div>
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
    <div class="col-md-3">
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
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="bi bi-currency-euro"></i>
            </div>
            <div>
                <div class="stat-number"><?php echo number_format($stats['valor_total'], 0, ',', '.'); ?>€</div>
                <div class="stat-label">Valor Total Vendido</div>
            </div>
        </div>
    </div>
</div>

<!-- Cars Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">
            <i class="bi bi-list-ul"></i>
            Lista de Viaturas Vendidas
        </h2>
    </div>
    
    <?php if (empty($cars)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 4rem; color: #e5e7eb;"></i>
            <p class="text-muted mt-3">Ainda não existem viaturas vendidas.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Imagem</th>
                        <th>Viatura</th>
                        <th>Matrícula</th>
                        <th>Ano</th>
                        <th>Quilómetros</th>
                        <th>Combustível</th>
                        <th>Preço</th>
                        <th>Data Venda</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cars as $car): ?>
                        <tr id="car-row-<?php echo $car['id']; ?>" class="row-sold">
                            <td>
                                <?php if (!empty($car['imagem_path']) && file_exists(__DIR__ . '/../uploads/' . $car['imagem_path'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($car['imagem_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($car['marca']); ?>">
                                <?php else: ?>
                                    <div style="width: 60px; height: 45px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($car['marca'] . ' ' . $car['modelo']); ?></strong>
                                <?php if (!empty($car['versao'])): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($car['versao']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($car['matricula'])): ?>
                                    <code style="background: #edf2f7; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600; color: #2d3748;">
                                        <?php echo htmlspecialchars($car['matricula']); ?>
                                    </code>
                                <?php else: ?>
                                    <span class="text-muted">---</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $car['ano']; ?></td>
                            <td><?php echo number_format($car['quilometros'], 0, ',', '.'); ?> km</td>
                            <td>
                                <?php 
                                $fuelStyles = [
                                    'Gasolina' => 'background: rgba(239, 68, 68, 0.15); color: #dc2626;',
                                    'Diesel' => 'background: rgba(59, 130, 246, 0.15); color: #2563eb;',
                                    'Híbrido' => 'background: rgba(34, 197, 94, 0.15); color: #16a34a;',
                                    'Elétrico' => 'background: rgba(234, 179, 8, 0.15); color: #ca8a04;',
                                    'GPL' => 'background: rgba(139, 92, 246, 0.15); color: #7c3aed;'
                                ];
                                $fuelStyle = $fuelStyles[$car['combustivel']] ?? 'background: rgba(107, 114, 128, 0.15); color: #4b5563;';
                                ?>
                                <span class="badge" style="<?php echo $fuelStyle; ?>"><?php echo htmlspecialchars($car['combustivel']); ?></span>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; align-items: flex-start;">
                                    <strong style="color: #22c55e; font-size: 1.1rem;">
                                        <?php echo number_format($car['preco'], 0, ',', '.'); ?> €
                                    </strong>
                                    <div style="display: flex; gap: 4px; margin-top: 2px;">
                                        <?php if (!empty($car['iva_incluido']) && $car['iva_incluido']): ?>
                                            <span style="font-size: 0.65rem; padding: 1px 4px; background: rgba(59, 130, 246, 0.1); color: #2563eb; border-radius: 4px; font-weight: 600;">IVA INC</span>
                                        <?php else: ?>
                                            <span style="font-size: 0.65rem; padding: 1px 4px; background: rgba(234, 179, 8, 0.1); color: #ca8a04; border-radius: 4px; font-weight: 600;">+IVA</span>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($car['garantia']) && $car['garantia']): ?>
                                            <span style="font-size: 0.65rem; padding: 1px 4px; background: rgba(34, 197, 94, 0.1); color: #16a34a; border-radius: 4px; font-weight: 600;" title="Com Garantia">GAR</span>
                                        <?php else: ?>
                                            <span style="font-size: 0.65rem; padding: 1px 4px; background: rgba(239, 68, 68, 0.1); color: #dc2626; border-radius: 4px; font-weight: 600;" title="Sem Garantia">S/ GAR</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Vendido
                                </span>
                                <?php if (!empty($car['data_venda'])): ?>
                                    <br><small class="text-muted"><?php echo date('d/m/Y', strtotime($car['data_venda'])); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <!-- Botão Repor como Disponível -->
                                    <button type="button" class="btn-action btn-unsell" 
                                            data-id="<?php echo $car['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($car['marca'] . ' ' . $car['modelo']); ?>"
                                            title="Marcar como disponível">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                    
                                    <a href="viatura.php?id=<?php echo $car['id']; ?>&return=<?php echo urlencode('vendidos.php'); ?>" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn-action btn-delete" 
                                            data-id="<?php echo $car['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($car['marca'] . ' ' . $car['modelo']); ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.row-sold {
    opacity: 0.85;
    background: rgba(34, 197, 94, 0.05);
}

.btn-unsell {
    background: rgba(59, 130, 246, 0.15);
    color: #2563eb;
    border: none;
    cursor: pointer;
}

.btn-unsell:hover {
    background: #2563eb;
    color: white;
}
</style>

<script>
// Repor como disponível
document.querySelectorAll('.btn-unsell').forEach(btn => {
    btn.addEventListener('click', function() {
        const carId = this.dataset.id;
        const carName = this.dataset.name;
        
        Swal.fire({
            title: 'Repor como Disponível?',
            html: `Voltar a colocar <strong>${carName}</strong> como disponível?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bi bi-arrow-counterclockwise me-1"></i> Sim, repor',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('marcar_vendido.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `id=${carId}&vendido=0`
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Reposto!',
                            text: 'Viatura está novamente disponível.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    }
                });
            }
        });
    });
});

// Delete functionality
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function() {
        const carId = this.dataset.id;
        const carName = this.dataset.name;
        
        Swal.fire({
            title: 'Eliminar Viatura?',
            html: `Tem a certeza que deseja eliminar <strong>${carName}</strong>?<br><small class="text-danger">Esta ação não pode ser revertida!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="bi bi-trash me-1"></i> Sim, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `delete.php?id=${carId}`;
            }
        });
    });
});

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
