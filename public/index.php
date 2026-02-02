<?php
/**
 * ============================================
 * Página Inicial - Estilo Paulimane
 * ============================================
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/../config/language.php';

$pageTitle = t('nav.home');

// Buscar últimos 6 carros DISPONÍVEIS (não vendidos)
try {
    $cars = dbQuery("SELECT * FROM cars WHERE vendido = 0 ORDER BY data_registo DESC LIMIT 6");
    
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
                // Evitar duplicar a principal se ela também estiver na galeria
                if ($img['imagem_path'] !== $car['imagem_path']) {
                    $allImages[] = $img['imagem_path'];
                }
            }
        } catch (Exception $e) {
            // Ignorar erros na galeria
        }
        
        $car['images'] = $allImages;
    }
    unset($car);
} catch (Exception $e) {
    $cars = [];
}

// Contar total de carros DISPONÍVEIS
try {
    $totalCars = dbQuery("SELECT COUNT(*) as total FROM cars WHERE vendido = 0")[0]['total'] ?? 0;
} catch (Exception $e) {
    $totalCars = 0;
}

require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section - Full Screen -->
<section class="hero-section">
    <div class="hero-carousel" id="heroCarousel">
        <?php
        // Fetch hero images from active configuration
        try {
            $heroImages = dbQuery("SELECT image_path FROM hero_images WHERE active = 1 ORDER BY created_at DESC");
            
            if (empty($heroImages)) {
                // Fallback if no images configured
                echo '<div class="hero-bg-image active" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);"></div>';
            } else {
                foreach ($heroImages as $index => $img) {
                    $activeClass = $index === 0 ? 'active' : '';
                    echo '<img src="../uploads/' . htmlspecialchars($img['image_path']) . '" class="hero-slide-img ' . $activeClass . '" alt="Hero ' . ($index + 1) . '">';
                }
            }
        } catch (Exception $e) {
            // Fallback on error
            echo '<div class="hero-bg-image active" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);"></div>';
        }
        ?>
        <div class="hero-overlay"></div>
    </div>
    
    <style>
    .hero-carousel {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: #1a1a2e;
        z-index: 0;
    }
    
    .hero-slide-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0;
        transition: opacity 1.5s ease-in-out;
        z-index: 1;
    }
    
    .hero-slide-img.active {
        opacity: 1;
        z-index: 2;
        animation: zoomEffect 7s ease-out forwards;
    }
    
    @keyframes zoomEffect {
        from { transform: scale(1); }
        to { transform: scale(1.05); }
    }
    
    .hero-section {
        position: relative;
    }
    
    .hero-overlay {
        position: absolute;
        inset: 0;
        z-index: 10;
        background: linear-gradient(90deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.4) 50%, rgba(0, 0, 0, 0.2) 100%);
        pointer-events: none; /* Let clicks pass through if needed */
    }
    
    .hero-content {
        position: relative;
        z-index: 20;
    }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.hero-slide-img');
        
        if (slides.length === 0) return;
        
        // Ensure first is active
        slides[0].classList.add('active');
        
        if (slides.length > 1) {
            let currentSlide = 0;
            const slideInterval = 5000;
            
            setInterval(() => {
                const prev = currentSlide;
                currentSlide = (currentSlide + 1) % slides.length;
                
                slides[prev].classList.remove('active');
                slides[currentSlide].classList.add('active');
            }, slideInterval);
        }
    });
    </script>
    <div class="hero-content">
        <h1 class="hero-title animate-fadeInUp">
            <?php echo t('home.hero_title_prefix'); ?> <span class="text-primary"><?php echo t('home.hero_title_highlight'); ?></span> <?php echo t('home.hero_title_suffix'); ?>
        </h1>
        <p class="hero-subtitle animate-fadeInUp delay-1">
            <?php echo nl2br(htmlspecialchars(getSetting('home_hero_subtitle'))); ?>
        </p>
        <div class="hero-cta animate-fadeInUp delay-2">
            <a href="stock.php" class="hero-cta-btn">
                <?php echo t('home.view_stock'); ?>
                <i class="bi bi-arrow-right"></i>
            </a>
            <a href="#footer" class="hero-cta-btn hero-cta-btn-outline">
                <i class="bi bi-telephone"></i>
                <?php echo t('home.contact_us'); ?>
            </a>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="scroll-indicator">
        <a href="#stats">
            <i class="bi bi-chevron-double-down"></i>
        </a>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section" id="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?php echo $totalCars; ?>+</div>
                <div class="stat-label"><?php echo t('home.stats.cars_in_stock'); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars(getSetting('home_stats_years')); ?></div>
                <div class="stat-label"><?php echo t('home.stats.years_experience'); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars(getSetting('home_stats_customers')); ?></div>
                <div class="stat-label"><?php echo t('home.stats.happy_customers'); ?></div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars(getSetting('home_stats_warranty')); ?></div>
                <div class="stat-label"><?php echo t('home.stats.warranty_years'); ?></div> <!-- Translates to 'Anos de Garantia', maybe need a new key for 'Com Garantia'? Using existing for now or adding new one? -->
            </div>
        </div>
    </div>
</section>

<!-- Últimas Adições -->
<section style="padding: 5rem 0; background: var(--bg-light);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php echo t('home.featured_title'); ?></h2>
            <div class="section-divider"></div>
            <p class="section-description">
                <?php echo t('home.featured_subtitle'); ?>
            </p>
        </div>
        
        <?php if (empty($cars)): ?>
            <div style="text-align: center; padding: 4rem 0;">
                <i class="bi bi-car-front" style="font-size: 5rem; color: var(--text-gray); opacity: 0.3;"></i>
                <p style="color: var(--text-gray); margin-top: 1rem;"><?php echo t('home.no_featured'); ?></p>
            </div>
        <?php else: ?>
            <div class="cars-grid">
                <?php foreach ($cars as $car): ?>
                    <?php include __DIR__ . '/includes/car_card.php'; ?>
                <?php endforeach; ?>
            </div>
            
            <!-- Ver Mais -->
            <div style="text-align: center; margin-top: 3rem;">
                <a href="stock.php" class="btn-outline">
                    <i class="bi bi-grid"></i>
                    <?php echo t('home.view_stock'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section style="padding: 5rem 0; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); text-align: center;">
    <div class="container">
        <h2 style="color: white; font-size: 2.25rem; font-weight: 700; margin-bottom: 1rem;">
            <?php echo t('home.cta_title'); ?>
        </h2>
        <p style="color: rgba(255,255,255,0.8); margin-bottom: 2rem; font-size: 1.15rem;">
            <?php echo t('home.cta_subtitle'); ?>
        </p>
        <a href="https://wa.me/<?php echo getSetting('whatsapp_number'); ?>" class="btn-whatsapp" target="_blank">
            <i class="bi bi-whatsapp"></i>
            <?php echo t('home.cta_button'); ?>
        </a>
    </div>
</section>

<style>
/* Emergency Restore Styles */
.car-card-image { position: relative; overflow: hidden; }
.sold-watermark, .reserved-watermark {
    position: absolute; top: 0; left: 0; right: 0; bottom: 0;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none; z-index: 5;
}
.sold-watermark { background: rgba(0, 0, 0, 0.3); }
.reserved-watermark { background: rgba(0, 0, 0, 0.2); }
.sold-watermark span, .reserved-watermark span {
    color: white; font-weight: 800; padding: 0.5rem 1.5rem; border-radius: 4px;
    transform: rotate(-15deg); box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    letter-spacing: 2px; text-transform: uppercase; font-size: 1.25rem;
}
.sold-watermark span { background: rgba(34, 197, 94, 0.95); }
.reserved-watermark span { background: rgba(234, 179, 8, 0.95); }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
