<?php
/**
 * ============================================
 * Stock Page - Estilo Paulimane
 * ============================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/language.php';

$pageTitle = t('stock.title');

// Aba ativa (disponíveis ou vendidos)
$tab = isset($_GET['tab']) && $_GET['tab'] === 'vendidos' ? 'vendidos' : 'disponiveis';

// Filtros
$filtroMarca = isset($_GET['marca']) ? trim($_GET['marca']) : '';
$filtroCombustivel = isset($_GET['combustivel']) ? trim($_GET['combustivel']) : '';
$filtroPrecoMax = isset($_GET['preco_max']) ? (float)$_GET['preco_max'] : 0;
$filtroAnoMin = isset($_GET['ano_min']) ? (int)$_GET['ano_min'] : 0;
$ordenar = isset($_GET['ordenar']) ? trim($_GET['ordenar']) : 'data_registo_desc';

// Build query
$sql = "SELECT * FROM cars WHERE vendido = " . ($tab === 'vendidos' ? '1' : '0');
$params = [];

if (!empty($filtroMarca)) {
    $sql .= " AND marca = :marca";
    $params[':marca'] = $filtroMarca;
}

if (!empty($filtroCombustivel)) {
    $sql .= " AND combustivel = :combustivel";
    $params[':combustivel'] = $filtroCombustivel;
}

if ($filtroPrecoMax > 0) {
    $sql .= " AND preco <= :preco_max";
    $params[':preco_max'] = $filtroPrecoMax;
}

if ($filtroAnoMin > 0) {
    $sql .= " AND ano >= :ano_min";
    $params[':ano_min'] = $filtroAnoMin;
}

// Ordenação
$ordens = [
    'data_registo_desc' => 'data_registo DESC',
    'preco_asc' => 'preco ASC',
    'preco_desc' => 'preco DESC',
    'ano_desc' => 'ano DESC',
    'quilometros_asc' => 'quilometros ASC'
];
$sql .= " ORDER BY " . ($ordens[$ordenar] ?? 'data_registo DESC');

try {
    $cars = dbQuery($sql, $params);
    
    // Buscar imagens de cada carro
    foreach ($cars as &$car) {
        $allImages = [];
        // 1. Imagem principal primeiro
        if (!empty($car['imagem_path'])) {
            $allImages[] = $car['imagem_path'];
        }
        
        // 2. Imagens da galeria depois
        try {
            $galleryImages = dbQuery(
                "SELECT imagem_path FROM car_images WHERE car_id = :car_id ORDER BY ordem ASC",
                [':car_id' => $car['id']]
            );
            foreach ($galleryImages as $img) {
                if ($img['imagem_path'] !== $car['imagem_path']) {
                    $allImages[] = $img['imagem_path'];
                }
            }
        } catch (Exception $e) {
            // Ignorar
        }
        
        $car['images'] = $allImages;
    }
    unset($car); // Limpar referência
    
    // Contadores para as abas
    $countDisponiveis = dbQuery("SELECT COUNT(*) as cnt FROM cars WHERE vendido = 0")[0]['cnt'];
    $countVendidos = dbQuery("SELECT COUNT(*) as cnt FROM cars WHERE vendido = 1")[0]['cnt'];
} catch (Exception $e) {
    $cars = [];
    $countDisponiveis = 0;
    $countVendidos = 0;
}

// Marcas para filtro
try {
    $marcas = dbQuery("SELECT DISTINCT marca FROM cars ORDER BY marca ASC");
} catch (Exception $e) {
    $marcas = [];
}

$anoAtual = (int)date('Y');

$noHero = true; // Navbar com fundo branco
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Header - Estilo Paulimane -->
<header class="page-header">
    <div class="container">
        <h1 class="page-title"><?php echo t('stock.title'); ?></h1>
        <p class="page-subtitle">
            <?php echo count($cars); ?> 
            <?php echo count($cars) !== 1 ? t('stock.subtitle_plural') : t('stock.subtitle_singular'); ?> 
            <?php 
                if ($tab === 'vendidos') {
                    echo count($cars) !== 1 ? t('stock.sold_plural') : t('stock.sold');
                } else {
                    echo count($cars) !== 1 ? t('stock.available_plural') : t('stock.available');
                }
            ?>
        </p>
    </div>
</header>

<!-- Tabs -->
<section class="stock-tabs">
    <div class="container">
        <div class="tabs-wrapper">
            <a href="stock.php?tab=disponiveis" class="tab-btn <?php echo $tab === 'disponiveis' ? 'active' : ''; ?>">
                <i class="bi bi-car-front"></i>
                <?php echo t('stock.tab_available'); ?>
                <span class="tab-count"><?php echo $countDisponiveis; ?></span>
            </a>
            <a href="stock.php?tab=vendidos" class="tab-btn <?php echo $tab === 'vendidos' ? 'active' : ''; ?>">
                <i class="bi bi-check-circle"></i>
                <?php echo t('stock.tab_sold'); ?>
                <span class="tab-count"><?php echo $countVendidos; ?></span>
            </a>
        </div>
    </div>
</section>

<!-- Filters Section -->
<section class="filters-section">
    <div class="container">
        <form id="filterForm" method="GET" action="">
            <input type="hidden" name="tab" value="<?php echo $tab; ?>">
            <div class="filters-row">
                <!-- Marca -->
                <div class="filter-group">
                    <label><?php echo t('stock.filters.brand'); ?></label>
                    <select name="marca">
                        <option value=""><?php echo t('stock.filters.all_brands'); ?></option>
                        <?php foreach ($marcas as $m): ?>
                            <option value="<?php echo htmlspecialchars($m['marca']); ?>" 
                                <?php echo $filtroMarca === $m['marca'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($m['marca']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Combustível -->
                <div class="filter-group">
                    <label><?php echo t('stock.filters.fuel'); ?></label>
                    <select name="combustivel">
                        <option value=""><?php echo t('stock.filters.all_fuels'); ?></option>
                        <?php 
                        $combustiveis = ['Gasolina', 'Diesel', 'Híbrido', 'Elétrico', 'GPL'];
                        foreach ($combustiveis as $c): 
                            $fuelLabel = t('fuels.' . $c, $c);
                        ?>
                            <option value="<?php echo $c; ?>" 
                                <?php echo $filtroCombustivel === $c ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($fuelLabel); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Preço Máx -->
                <div class="filter-group">
                    <label><?php echo t('stock.filters.max_price'); ?></label>
                    <input type="number" name="preco_max" placeholder="Ex: 30000" 
                           value="<?php echo $filtroPrecoMax > 0 ? $filtroPrecoMax : ''; ?>"
                           min="0" step="1000">
                </div>
                
                <!-- Ano Mínimo -->
                <div class="filter-group">
                    <label><?php echo t('stock.filters.min_year'); ?></label>
                    <select name="ano_min">
                        <option value=""><?php echo t('stock.filters.any_year'); ?></option>
                        <?php for ($a = $anoAtual; $a >= $anoAtual - 15; $a--): ?>
                            <option value="<?php echo $a; ?>" <?php echo $filtroAnoMin === $a ? 'selected' : ''; ?>>
                                <?php echo $a; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <!-- Ordenar -->
                <div class="filter-group">
                    <label><?php echo t('stock.filters.sort'); ?></label>
                    <select name="ordenar">
                        <option value="data_registo_desc" <?php echo $ordenar === 'data_registo_desc' ? 'selected' : ''; ?>><?php echo t('stock.filters.sort_recent'); ?></option>
                        <option value="preco_asc" <?php echo $ordenar === 'preco_asc' ? 'selected' : ''; ?>><?php echo t('stock.filters.sort_price_low'); ?></option>
                        <option value="preco_desc" <?php echo $ordenar === 'preco_desc' ? 'selected' : ''; ?>><?php echo t('stock.filters.sort_price_high'); ?></option>
                        <option value="ano_desc" <?php echo $ordenar === 'ano_desc' ? 'selected' : ''; ?>><?php echo t('stock.filters.sort_year_new'); ?></option>
                        <option value="quilometros_asc" <?php echo $ordenar === 'quilometros_asc' ? 'selected' : ''; ?>><?php echo t('stock.filters.sort_km_low'); ?></option>
                    </select>
                </div>
                
                <!-- Botão -->
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="filter-btn">
                        <i class="bi bi-search"></i> <?php echo t('stock.filters.filter_btn'); ?>
                    </button>
                </div>
            </div>
            
            <?php if (!empty($filtroMarca) || !empty($filtroCombustivel) || $filtroPrecoMax > 0 || $filtroAnoMin > 0): ?>
                <div style="text-align: center; margin-top: 1rem;">
                    <a href="stock.php?tab=<?php echo $tab; ?>" class="btn-outline" style="padding: 0.5rem 1.25rem; font-size: 0.9rem;">
                        <i class="bi bi-x-circle"></i> <?php echo t('stock.filters.clear_filters'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</section>

<!-- Cars Grid -->
<section style="padding: 3rem 0 5rem; background: var(--bg-light);">
    <div class="container">
        <?php if (empty($cars)): ?>
            <div style="text-align: center; padding: 5rem 0;">
                <i class="bi bi-search" style="font-size: 5rem; color: var(--text-gray); opacity: 0.3;"></i>
                <h3 style="margin-top: 1.5rem; color: var(--text-dark);">
                    <?php echo $tab === 'vendidos' ? t('stock.no_sold') : t('stock.no_results'); ?>
                </h3>
                <p style="color: var(--text-gray); margin-bottom: 2rem;">
                    <?php echo $tab === 'vendidos' ? t('stock.history_msg') : t('stock.try_filters'); ?>
                </p>
                <?php if ($tab === 'disponiveis'): ?>
                    <a href="stock.php" class="btn-primary">
                        <i class="bi bi-arrow-left"></i> <?php echo t('stock.back_to_stock'); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="cars-grid">
                <?php foreach ($cars as $car): ?>
                    <?php include __DIR__ . '/includes/car_card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>



<?php require_once __DIR__ . '/../includes/footer.php'; ?>
