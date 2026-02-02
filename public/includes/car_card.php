<?php
/**
 * Componente Car Card
 * 
 * Requer a variável $car (array com dados da viatura)
 */

// Se não houver array de imagens mas houver imagem_path, cria o array
if (!isset($car['images']) || empty($car['images'])) {
    $car['images'] = !empty($car['imagem_path']) ? [$car['imagem_path']] : [];
}
?>
<article class="car-card animate-fadeInUp <?php 
    echo (!empty($car['vendido']) && $car['vendido']) ? 'car-sold' : ''; 
    echo (!empty($car['reservado']) && $car['reservado']) ? ' car-reserved' : ''; 
?>">
    <div class="car-card-image <?php echo count($car['images']) > 1 ? 'card-gallery' : ''; ?>" 
         <?php if (count($car['images']) > 1): ?>
         data-images='<?php echo json_encode($car['images']); ?>' data-index="0"
         <?php endif; ?>>
        
        <?php if (!empty($car['images'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($car['images'][0]); ?>" 
                 alt="<?php echo htmlspecialchars($car['marca'] . ' ' . $car['modelo']); ?>"
                 loading="lazy"
                 class="gallery-main-img">
            
            <?php if (count($car['images']) > 1): ?>
                <!-- Setas de Navegação -->
                <button class="card-nav card-nav-prev" onclick="cardNavigate(this, -1, event)" title="Anterior">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="card-nav card-nav-next" onclick="cardNavigate(this, 1, event)" title="Próxima">
                    <i class="bi bi-chevron-right"></i>
                </button>
                <!-- Indicadores -->
                <div class="card-gallery-dots">
                    <?php foreach ($car['images'] as $imgIndex => $img): ?>
                        <span class="dot <?php echo $imgIndex === 0 ? 'active' : ''; ?>"></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="car-card-no-image" style="height: 200px; display: flex; align-items: center; justify-content: center; background: #f3f4f6; color: #9ca3af;">
                <i class="bi bi-car-front" style="font-size: 3rem;"></i>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($car['vendido']) && $car['vendido']): ?>
            <div class="sold-watermark">
                <span><?php echo t('stock.sold_label'); ?></span>
            </div>
        <?php elseif (!empty($car['reservado']) && $car['reservado']): ?>
            <div class="reserved-watermark">
                <span><?php echo t('stock.reserved_label'); ?></span>
            </div>
        <?php elseif (isset($car['quilometros']) && $car['quilometros'] < (int)getSetting('semi_new_max_km', 10000)): ?>
            <span class="car-card-badge"><?php echo t('details.semi_new'); ?></span>
        <?php elseif (isset($car['ano']) && $car['ano'] >= date('Y')): ?>
            <span class="car-card-badge"><?php echo t('details.new'); ?></span>
        <?php elseif (isset($car['ano']) && $car['ano'] >= date('Y') - 1): ?>
            <span class="car-card-badge"><?php echo t('details.new'); ?></span> <!-- Using 'New' for Recent as well or create new key? Let's assume 'New' is close enough or use Recent key if available -->
        <?php endif; ?>
    </div>
    
    <div class="car-card-body">
        <h3 class="car-card-title">
            <?php echo htmlspecialchars($car['marca'] . ' ' . $car['modelo']); ?>
        </h3>
        
        <?php if (!empty($car['versao'])): ?>
            <p class="car-card-version"><?php echo htmlspecialchars($car['versao']); ?></p>
        <?php endif; ?>
        
        <div class="car-card-specs">
            <span class="car-card-spec">
                <i class="bi bi-calendar3"></i>
                <?php echo htmlspecialchars($car['ano']); ?>
            </span>
            <span class="car-card-spec">
                <i class="bi bi-speedometer2"></i>
                <?php echo number_format($car['quilometros'], 0, ',', '.'); ?> km
            </span>
            <span class="car-card-spec">
                <i class="bi bi-fuel-pump"></i>
                <?php 
                $fuelKey = 'fuels.' . $car['combustivel'];
                echo t($fuelKey, $car['combustivel']); 
                ?>
            </span>
            <?php if (!empty($car['garantia']) && $car['garantia']): ?>
                <span class="car-card-spec" title="<?php echo t('details.warranty_included'); ?>">
                    <i class="bi bi-shield-check" style="color: #16a34a !important;"></i>
                </span>
            <?php else: ?>
                <span class="car-card-spec" title="<?php echo t('details.warranty_not_included'); ?>">
                    <i class="bi bi-shield-x" style="color: #dc2626 !important;"></i>
                </span>
            <?php endif; ?>
        </div>
        
        <div class="car-card-price">
            <?php echo number_format($car['preco'], 0, ',', '.'); ?> €
            <small><?php echo (!empty($car['iva_incluido']) && $car['iva_incluido']) ? t('details.vat_included') : t('details.vat_excluded'); ?></small>
        </div>
        
        <a href="detalhes.php?id=<?php echo (int)$car['id']; ?>" class="btn-card">
            <?php echo t('home.view_details'); ?> <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</article>
