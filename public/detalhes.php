<?php
/**
 * ============================================
 * Detalhes Page
 * ============================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/language.php';

$carId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($carId <= 0) {
    header('Location: stock.php');
    exit;
}

try {
    $cars = dbQuery("SELECT * FROM cars WHERE id = :id LIMIT 1", [':id' => $carId]);
} catch (Exception $e) {
    header('Location: stock.php');
    exit;
}

if (empty($cars)) {
    header('Location: stock.php');
    exit;
}

$car = $cars[0];
$pageTitle = $car['marca'] . ' ' . $car['modelo'];

// Carros relacionados
try {
    $related = dbQuery(
        "SELECT * FROM cars WHERE marca = :marca AND id != :id AND vendido = 0 ORDER BY data_registo DESC LIMIT 3",
        [':marca' => $car['marca'], ':id' => $carId]
    );
} catch (Exception $e) {
    $related = [];
}

// Obter lista final de imagens (Principal primeiro)
$finalImages = [];
if (!empty($car['imagem_path'])) {
    $finalImages[] = ['imagem_path' => $car['imagem_path']];
}

try {
    $gallery = dbQuery(
        "SELECT imagem_path FROM car_images WHERE car_id = :car_id ORDER BY ordem ASC",
        [':car_id' => $carId]
    );
    foreach ($gallery as $img) {
        if ($img['imagem_path'] !== $car['imagem_path']) {
            $finalImages[] = ['imagem_path' => $img['imagem_path']];
        }
    }
} catch (Exception $e) {
    // Ignorar
}

$images = $finalImages;

$noHero = true; // Página sem hero - navbar deve ter fundo
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Car Details Section -->
<section class="car-details-section">
    <div class="container">
            <!-- Breadcrumb -->
        <nav style="margin-bottom: 2rem;">
            <a href="index.php" style="color: var(--text-gray); text-decoration: none;"><?php echo t('nav.home'); ?></a>
            <span style="color: var(--text-gray); margin: 0 0.5rem;">/</span>
            <a href="stock.php" style="color: var(--text-gray); text-decoration: none;"><?php echo t('nav.stock'); ?></a>
            <span style="color: var(--text-gray); margin: 0 0.5rem;">/</span>
            <span style="color: var(--primary-color);"><?php echo htmlspecialchars($car['marca'] . ' ' . $car['modelo']); ?></span>
        </nav>
        
        <div class="car-details-grid">
            <!-- Galeria de Imagens -->
            <div>
                <!-- Imagem Principal -->
                <div class="car-details-image gallery-container" id="mainImageContainer">
                    <?php if (!empty($images)): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($images[0]['imagem_path']); ?>" 
                             alt="<?php echo htmlspecialchars($car['marca'] . ' ' . $car['modelo']); ?>"
                             id="mainImage">
                        
                        <?php if (count($images) > 1): ?>
                            <!-- Setas de Navegação -->
                            <button class="gallery-nav gallery-prev" onclick="navigateGallery(-1)" title="Anterior">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button class="gallery-nav gallery-next" onclick="navigateGallery(1)" title="Próxima">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                            
                            <!-- Contador de Imagens -->
                            <div class="gallery-counter">
                                <span id="currentImageIndex">1</span> / <?php echo count($images); ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Botão Expandir -->
                        <button class="gallery-expand" onclick="openFullscreen(document.getElementById('mainImage').src)" title="Ver em ecrã inteiro">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </button>
                    <?php else: ?>
                        <div class="no-image" style="height: 450px;">
                            <i class="bi bi-car-front" style="font-size: 6rem;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Miniaturas - Carrossel Deslizante -->
                <?php if (count($images) > 1): ?>
                    <div class="thumbnails-carousel-container">
                        <div class="image-thumbnails" id="thumbnailsContainer">
                            <?php foreach ($images as $index => $img): ?>
                                <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                                     data-index="<?php echo $index; ?>"
                                     onclick="changeMainImage('<?php echo htmlspecialchars($img['imagem_path']); ?>', this)">
                                    <img src="../uploads/<?php echo htmlspecialchars($img['imagem_path']); ?>" 
                                         alt="<?php echo sprintf(t('details.img_count'), $index + 1); ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Info -->
            <div class="car-details-info">
                <div style="margin-bottom: 1rem;">
                    <?php if (!empty($car['vendido']) && $car['vendido']): ?>
                        <span style="display: inline-block; background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 0.5rem 1.25rem; border-radius: 20px; font-size: 0.95rem; font-weight: 600;">
                            <i class="bi bi-check-circle-fill me-1"></i> <?php echo t('stock.sold_label'); ?>
                        </span>
                    <?php elseif (!empty($car['reservado']) && $car['reservado']): ?>
                        <span style="display: inline-block; background: linear-gradient(135deg, #eab308, #ca8a04); color: white; padding: 0.5rem 1.25rem; border-radius: 20px; font-size: 0.95rem; font-weight: 600;">
                            <i class="bi bi-bookmark-fill me-1"></i> <?php echo t('stock.reserved_label'); ?>
                        </span>
                    <?php elseif ($car['ano'] >= date('Y')): ?>
                        <span style="display: inline-block; background: var(--primary-color); color: white; padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                            <?php echo t('details.new'); ?>
                        </span>
                    <?php elseif ($car['quilometros'] < (int)getSetting('semi_new_max_km', 10000)): ?>
                        <span style="display: inline-block; background: var(--primary-color); color: white; padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                            <?php echo t('details.semi_new'); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <h1><?php echo htmlspecialchars($car['marca'] . ' ' . $car['modelo']); ?></h1>
                
                <?php if (!empty($car['versao'])): ?>
                    <p class="car-details-version"><?php echo htmlspecialchars($car['versao']); ?></p>
                <?php endif; ?>
                
                <!-- Preço -->
                <div class="car-details-price">
                    <span class="price"><?php echo number_format($car['preco'], 0, ',', '.'); ?> €</span>
                    <span class="iva"><?php echo (!empty($car['iva_incluido']) && $car['iva_incluido']) ? t('details.vat_included') : t('details.vat_excluded'); ?></span>
                </div>
                
                <!-- Specs Quick -->
                <div class="specs-quick">
                    <div class="spec-box">
                        <i class="bi bi-calendar3"></i>
                        <span class="label"><?php echo t('details.year'); ?></span>
                        <span class="value"><?php echo htmlspecialchars($car['ano']); ?></span>
                    </div>
                    <div class="spec-box">
                        <i class="bi bi-speedometer2"></i>
                        <span class="label"><?php echo t('details.km'); ?></span>
                        <span class="value"><?php echo number_format($car['quilometros'], 0, ',', '.'); ?></span>
                    </div>
                    <div class="spec-box">
                        <i class="bi bi-fuel-pump"></i>
                        <span class="label"><?php echo t('details.fuel'); ?></span>
                        <span class="value"><?php echo t('fuels.' . $car['combustivel'], $car['combustivel']); ?></span>
                    </div>

                    <div class="spec-box">
                        <i class="bi bi-shield-<?php echo (!empty($car['garantia']) && $car['garantia']) ? 'check' : 'x'; ?>"></i>
                        <span class="label"><?php echo t('details.warranty'); ?></span>
                        <span class="value"><?php echo (!empty($car['garantia']) && $car['garantia']) ? t('details.warranty_included') : t('details.warranty_not_included'); ?></span>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="car-details-actions">
                    <a href="https://wa.me/<?php echo getSetting('whatsapp_number'); ?>?text=<?php echo urlencode(sprintf(t('details.contact_msg_template'), $car['marca'] . ' ' . $car['modelo'], $car['id'])); ?>" 
                       class="btn-whatsapp" target="_blank">
                        <i class="bi bi-whatsapp"></i>
                        <?php echo t('details.contact_whatsapp'); ?>
                    </a>
                    <a href="tel:+351912345678" class="btn-outline">
                        <i class="bi bi-telephone"></i>
                        <?php echo t('details.call_now'); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Description & Specs -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 3rem;">
            <!-- Descrição -->
            <div class="specs-card">
                <h3><i class="bi bi-info-circle"></i> <?php echo t('details.description'); ?></h3>
                <?php if (!empty($car['descricao'])): ?>
                    <p style="color: var(--text-gray); line-height: 1.8; white-space: pre-line;">
                        <?php echo t('cars.' . $car['id'] . '.description', $car['descricao']); ?>
                    </p>
                <?php else: ?>
                    <p style="color: var(--text-gray);">
                        Contacte-nos para mais informações sobre esta viatura.
                    </p>
                <?php endif; ?>
            </div>
            
            <!-- Especificações -->
            <div class="specs-card">
                <h3><i class="bi bi-list-check"></i> <?php echo t('details.tech_specs'); ?></h3>
                
                <!-- Informações Gerais -->
                <h4 style="color: var(--primary-color); font-size: 1rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 600;"><?php echo t('details.general_info'); ?></h4>
                <table class="specs-table">
                    <tr>
                        <th><?php echo t('details.reference'); ?></th>
                        <td>#<?php echo $car['id']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo t('details.brand'); ?></th>
                        <td><?php echo htmlspecialchars($car['marca']); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo t('details.model'); ?></th>
                        <td><?php echo htmlspecialchars($car['modelo']); ?></td>
                    </tr>
                    <?php if (!empty($car['versao'])): ?>
                    <tr>
                        <th><?php echo t('details.version'); ?></th>
                        <td><?php echo htmlspecialchars($car['versao']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th><?php echo t('details.year'); ?></th>
                        <td><?php echo $car['ano']; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo t('details.km'); ?></th>
                        <td><?php echo number_format($car['quilometros'], 0, ',', '.'); ?> km</td>
                    </tr>
                    <?php if (!empty($car['segmento'])): ?>
                    <tr>
                        <th><?php echo t('details.segment'); ?></th>
                        <td><?php echo htmlspecialchars($car['segmento']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
                
                <!-- Motor e Desempenho -->
                <?php if (!empty($car['potencia']) || !empty($car['cilindrada']) || !empty($car['combustivel'])): ?>
                <h4 style="color: var(--primary-color); font-size: 1rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 600;"><?php echo t('details.engine_performance'); ?></h4>
                <table class="specs-table">
                    <tr>
                        <th><?php echo t('details.fuel'); ?></th>
                        <td><?php echo t('fuels.' . $car['combustivel'], $car['combustivel']); ?></td>
                    </tr>
                    <?php if (!empty($car['potencia'])): ?>
                    <tr>
                        <th><?php echo t('details.power'); ?></th>
                        <td><?php echo htmlspecialchars($car['potencia']); ?> cv</td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($car['cilindrada'])): ?>
                    <tr>
                        <th><?php echo t('details.displacement'); ?></th>
                        <td><?php echo number_format($car['cilindrada'], 0, ',', '.'); ?> cm³</td>
                    </tr>
                    <?php endif; ?>
                </table>
                <?php endif; ?>
                
                <!-- Transmissão -->
                <?php if (!empty($car['transmissao']) || !empty($car['tracao'])): ?>
                <h4 style="color: var(--primary-color); font-size: 1rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 600;"><?php echo t('details.transmission_section'); ?></h4>
                <table class="specs-table">
                    <?php if (!empty($car['transmissao'])): ?>
                    <tr>
                        <th><?php echo t('details.type'); ?></th>
                        <td><?php echo htmlspecialchars($car['transmissao']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($car['tracao'])): ?>
                    <tr>
                        <th><?php echo t('details.traction'); ?></th>
                        <td><?php echo htmlspecialchars($car['tracao']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
                <?php endif; ?>

                <!-- Configuração -->
                <?php if (!empty($car['portas']) || !empty($car['lugares'])): ?>
                <h4 style="color: var(--primary-color); font-size: 1rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 600;"><?php echo t('details.configuration'); ?></h4>
                <table class="specs-table">
                    <?php if (!empty($car['portas'])): ?>
                    <tr>
                        <th><?php echo t('details.doors'); ?></th>
                        <td><?php echo htmlspecialchars($car['portas']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($car['lugares'])): ?>
                    <tr>
                        <th><?php echo t('details.seats'); ?></th>
                        <td><?php echo htmlspecialchars($car['lugares']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
                <?php endif; ?>

                <!-- Estética -->
                <?php if (!empty($car['cor']) || !empty($car['cor_interior'])): ?>
                <h4 style="color: var(--primary-color); font-size: 1rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 600;"><?php echo t('details.aesthetics'); ?></h4>
                <table class="specs-table">
                    <?php if (!empty($car['cor'])): ?>
                    <tr>
                        <th><?php echo t('details.exterior_color'); ?></th>
                        <td><?php echo htmlspecialchars($car['cor']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($car['cor_interior'])): ?>
                    <tr>
                        <th><?php echo t('details.interior_color'); ?></th>
                        <td><?php echo htmlspecialchars($car['cor_interior']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
                <?php endif; ?>
                
                <!-- Eficiência -->
                <?php if (!empty($car['consumo_medio']) || !empty($car['emissoes_co2'])): ?>
                <h4 style="color: var(--primary-color); font-size: 1rem; margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 600;"><?php echo t('details.efficiency'); ?></h4>
                <table class="specs-table">
                    <?php if (!empty($car['consumo_medio'])): ?>
                    <tr>
                        <th><?php echo t('details.avg_consumption'); ?></th>
                        <td><?php echo number_format($car['consumo_medio'], 1, ',', '.'); ?> L/100km</td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($car['emissoes_co2'])): ?>
                    <tr>
                        <th><?php echo t('details.co2_emissions'); ?></th>
                        <td><?php echo $car['emissoes_co2']; ?> g/km</td>
                    </tr>
                    <?php endif; ?>
                </table>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Relacionados -->
        <?php if (!empty($related)): ?>
            <div style="margin-top: 4rem; padding-top: 3rem; border-top: 1px solid #e5e7eb;">
                <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;">
                    <?php echo t('details.related_cars'); ?> <?php echo htmlspecialchars($car['marca']); ?>
                </h3>
                <p style="color: var(--text-gray); margin-bottom: 2rem;">
                    <?php echo t('details.related_subtitle'); ?>
                </p>
                
                <div class="cars-grid">
                    <?php foreach ($related as $relatedCar): 
                        // Temporária variável para reutilizar o template
                        $car = $relatedCar;
                        // Imagens para o card
                        $car['images'] = !empty($car['imagem_path']) ? [$car['imagem_path']] : [];
                        include __DIR__ . '/includes/car_card.php';
                    endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
                



<!-- Related Cars -->
<?php if (!empty($related)): ?>
<section style="padding: 4rem 0; background: var(--bg-light);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Outros <?php echo htmlspecialchars($car['marca']); ?></h2>
            <div class="section-divider"></div>
            <p class="section-description">Veja também estas opções da mesma marca</p>
        </div>
        
        <div class="cars-grid" style="grid-template-columns: repeat(3, 1fr);">
            <?php foreach ($related as $rel): ?>
                <article class="car-card">
                    <div class="car-card-image">
                        <?php if (!empty($rel['imagem_path']) && file_exists(__DIR__ . '/../uploads/' . $rel['imagem_path'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($rel['imagem_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($rel['marca'] . ' ' . $rel['modelo']); ?>">
                        <?php else: ?>
                            <div class="no-image" style="height: 100%;"><i class="bi bi-car-front"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="car-card-body">
                        <h3 class="car-card-title"><?php echo htmlspecialchars($rel['marca'] . ' ' . $rel['modelo']); ?></h3>
                        <div class="car-card-specs">
                            <span class="car-card-spec"><i class="bi bi-calendar3"></i> <?php echo $rel['ano']; ?></span>
                            <span class="car-card-spec"><i class="bi bi-speedometer2"></i> <?php echo number_format($rel['quilometros'], 0, ',', '.'); ?> km</span>
                        </div>
                        <div class="car-card-price"><?php echo number_format($rel['preco'], 0, ',', '.'); ?> €</div>
                        <a href="detalhes.php?id=<?php echo $rel['id']; ?>" class="btn-card">Ver Detalhes <i class="bi bi-arrow-right"></i></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Estilos da Galeria -->
<style>
/* Container do carrossel de miniaturas */
.thumbnails-carousel-container {
    position: relative;
    margin-top: 1rem;
    overflow: hidden;
    max-width: 644px; /* 7 thumbnails × (80px + 12px gap) - 12px final gap */
}

.image-thumbnails {
    display: flex;
    gap: 0.75rem;
    transition: transform 0.3s ease;
}

.thumbnail {
    flex-shrink: 0;
    width: 80px;
    height: 60px;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.2s ease;
    opacity: 0.5;
}

.thumbnail:hover {
    opacity: 0.8;
    border-color: var(--primary-light);
}

.thumbnail.active {
    opacity: 1;
    border-color: var(--primary-color);
    transform: scale(1.05);
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

#mainImage {
    transition: opacity 0.3s ease;
}

/* Gallery Container */
.gallery-container {
    position: relative;
}

/* Navigation Arrows */
.gallery-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #1a1a1a;
    opacity: 0;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    z-index: 10;
}

.gallery-container:hover .gallery-nav {
    opacity: 1;
}

.gallery-nav:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-50%) scale(1.1);
}

.gallery-prev {
    left: 1rem;
}

.gallery-next {
    right: 1rem;
}

/* Image Counter */
.gallery-counter {
    position: absolute;
    bottom: 1rem;
    right: 1rem;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.gallery-expand {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #1a1a1a;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    z-index: 10;
    opacity: 0;
}

.gallery-container:hover .gallery-expand {
    opacity: 1;
}

.gallery-expand:hover {
    background: var(--primary-color);
    color: white;
    transform: scale(1.1);
}

/* Modal de Ecrã Inteiro */
.image-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 50px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.9);
    align-items: center;
    justify-content: center;
}

.modal-content {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 90vh;
    object-fit: contain;
    animation: zoomIn 0.3s;
}

@keyframes zoomIn {
    from {transform:scale(0)}
    to {transform:scale(1)}
}

.close-modal {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
    cursor: pointer;
    z-index: 10000;
}

.close-modal:hover,
.close-modal:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}
</style>

<!-- Script da Galeria -->
<script>
// Array de imagens
const galleryImages = <?php echo json_encode(array_column($images, 'imagem_path')); ?>;
let currentIndex = 0;

function changeMainImage(imagePath, thumbnailElement) {
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.style.opacity = '0.5';
        setTimeout(() => {
            mainImage.src = '../uploads/' + imagePath;
            mainImage.style.opacity = '1';
        }, 150);
    }
    
    // Atualizar índice atual
    currentIndex = galleryImages.indexOf(imagePath);
    updateCounter();
    
    // Atualizar classe active
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    if (thumbnailElement) {
        thumbnailElement.classList.add('active');
    }
    
    // Deslizar carrossel de miniaturas
    updateThumbnailsCarousel();
}

function updateThumbnailsCarousel() {
    const container = document.getElementById('thumbnailsContainer');
    if (!container) return;
    
    const thumbnails = container.querySelectorAll('.thumbnail');
    if (thumbnails.length <= 7) {
        // Se tiver 7 ou menos imagens, não precisa deslizar
        container.style.transform = 'translateX(0)';
        return;
    }
    
    // Calcular quantas miniaturas mostrar antes e depois da atual
    const visibleCount = 7; // Total de miniaturas visíveis
    const sideThumbs = Math.floor((visibleCount - 1) / 2); // 3 de cada lado
    
    // Calcular o índice inicial para centralizar a miniatura ativa
    let startIndex = currentIndex - sideThumbs;
    
    // Ajustar se estiver no início
    if (startIndex < 0) {
        startIndex = 0;
    }
    
    // Ajustar se estiver no final
    if (startIndex + visibleCount > thumbnails.length) {
        startIndex = thumbnails.length - visibleCount;
    }
    
    // Calcular o deslocamento (cada miniatura tem 80px + 12px de gap)
    const thumbnailWidth = 80 + 12; // largura + gap
    const offset = startIndex * thumbnailWidth;
    
    container.style.transform = `translateX(-${offset}px)`;
}

function navigateGallery(direction) {
    currentIndex += direction;
    
    // Loop circular
    if (currentIndex < 0) {
        currentIndex = galleryImages.length - 1;
    } else if (currentIndex >= galleryImages.length) {
        currentIndex = 0;
    }
    
    const imagePath = galleryImages[currentIndex];
    const mainImage = document.getElementById('mainImage');
    
    if (mainImage) {
        mainImage.style.opacity = '0.5';
        setTimeout(() => {
            mainImage.src = '../uploads/' + imagePath;
            mainImage.style.opacity = '1';
        }, 150);
    }
    
    updateCounter();
    updateThumbnails();
}

function updateCounter() {
    const counter = document.getElementById('currentImageIndex');
    if (counter) {
        counter.textContent = currentIndex + 1;
    }
}

function updateThumbnails() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach((thumb, index) => {
        thumb.classList.toggle('active', index === currentIndex);
    });
    
    // Atualizar posição do carrossel
    updateThumbnailsCarousel();
}

// Navegação por teclado
document.addEventListener('keydown', function(e) {
    if (galleryImages.length > 1) {
        if (e.key === 'ArrowLeft') {
            navigateGallery(-1);
        } else if (e.key === 'ArrowRight') {
            navigateGallery(1);
        }
    }
});

function openFullscreen(src) {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("fullImage");
    modal.style.display = "flex";
    modalImg.src = src;
    document.body.style.overflow = "hidden";
}

function closeFullscreen() {
    const modal = document.getElementById("imageModal");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
}

document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeFullscreen();
    }
});
</script>

<!-- Modal Structure -->
<div id="imageModal" class="image-modal" onclick="closeFullscreen()">
    <span class="close-modal">&times;</span>
    <img class="modal-content" id="fullImage">
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
