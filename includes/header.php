<?php
/**
 * Header
 * Navbar transparente que fica branca ao scroll
 */

require_once __DIR__ . '/../config/language.php';
require_once __DIR__ . '/../config/settings.php';

// Ensure settings are loaded
if (!isset($settings)) {
    $settings = getAllSettings();
}

$siteName = $settings['company_name'] ?? 'Stand Automóvel';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$langCode = getCurrentLanguage();
?>
<!DOCTYPE html>
<html lang="<?php echo $langCode; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($siteName); ?> - Os melhores carros usados com garantia. Visite-nos e encontre o seu próximo veículo.">
    <meta name="keywords" content="carros usados, stand automóvel, viaturas, automóveis, comprar carro">
    
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; ?><?php echo htmlspecialchars($siteName); ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/styles.css?v=<?php echo time(); ?>_9" rel="stylesheet">
    
    <!-- Flag Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@6.6.6/css/flag-icons.min.css"/>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar<?php echo isset($noHero) && $noHero ? ' scrolled always-scrolled' : ''; ?>" id="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="index.php" class="logo">
                    <i class="bi bi-car-front-fill"></i>
                    <span><?php echo htmlspecialchars($siteName); ?></span>
                </a>
                
                <ul class="nav-menu" id="navMenu">
                    <li>
                        <a href="index.php" class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>">
                            <?php echo t('nav.home'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="stock.php" class="nav-link <?php echo $currentPage === 'stock' ? 'active' : ''; ?>">
                            <?php echo t('nav.stock'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="contacto.php" class="nav-link <?php echo $currentPage === 'contacto' ? 'active' : ''; ?>">
                            <?php echo t('nav.contact'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="stock.php" class="nav-btn <?php echo isset($noHero) && $noHero && false ? 'btn-primary' : ''; ?>">
                            <i class="bi bi-search"></i> <?php echo t('nav.view_cars'); ?>
                        </a>
                    </li>
                    
                    <!-- Theme Toggle -->
                    <li>
                        <button onclick="toggleTheme()" class="theme-toggle-btn" aria-label="Alternar Tema">
                            <i class="bi bi-moon-fill" id="themeIcon"></i>
                        </button>
                    </li>

                    <!-- Language Selector -->
                    <li class="lang-dropdown-wrapper">
                        <?php 
                        $currLang = getCurrentLanguage(); 
                        $flags = ['pt' => 'pt', 'en' => 'gb', 'es' => 'es'];
                        $names = ['pt' => 'Português', 'en' => 'English', 'es' => 'Español'];
                        ?>
                        <div class="lang-selector" onclick="toggleLangDropdown(event)">
                            <span class="fi fi-<?php echo $flags[$currLang]; ?>"></span>
                            <span><?php echo strtoupper($currLang); ?></span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <ul class="lang-dropdown" id="langDropdown">
                            <?php foreach (AVAILABLE_LANGUAGES as $l): ?>
                                <li>
                                    <?php 
                                    // Build URL with new lang param, preserving other params
                                    $params = $_GET;
                                    $params['lang'] = $l;
                                    $langUrl = '?' . http_build_query($params);
                                    ?>
                                    <a href="<?php echo $langUrl; ?>" class="<?php echo $currLang === $l ? 'active' : ''; ?>">
                                        <span class="fi fi-<?php echo $flags[$l]; ?>"></span>
                                        <?php echo $names[$l]; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
                
                <button class="hamburger" id="hamburger" onclick="toggleMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>
    
    <script>
    function toggleLangDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('langDropdown');
        dropdown.classList.toggle('show');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('langDropdown');
        const wrapper = document.querySelector('.lang-dropdown-wrapper');
        
        if (dropdown && dropdown.classList.contains('show') && !wrapper.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });
    </script>
    
    <style>
    /* Language Selector Styles */
    .lang-dropdown-wrapper {
        position: relative;
        margin-left: 1rem;
    }
    
    .lang-selector {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        padding: 0.5rem 0.75rem;
        border-radius: 20px;
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.2s;
        user-select: none;
        white-space: nowrap;
    }
    
    .lang-selector:hover {
        background: rgba(255,255,255,0.1);
    }

    /* Style for scrolled navbar */
    .navbar.scrolled .lang-selector {
        color: var(--text-dark) !important;
        border-color: rgba(0,0,0,0.1);
    }
    
    .navbar.scrolled .lang-selector:hover {
        background: rgba(0,0,0,0.05);
    }
    
    .lang-dropdown {
        position: absolute;
        top: 120%; /* Space between selector and dropdown */
        right: 0;
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        padding: 0.5rem;
        min-width: 160px;
        display: none;
        flex-direction: column;
        gap: 0.25rem;
        margin-top: 0;
        z-index: 10000; /* High z-index to stay on top */
        border: 1px solid rgba(0,0,0,0.05); /* Subtle border */
        list-style: none;
    }
    
    .lang-dropdown.show {
        display: flex;
        animation: fadeInDown 0.2s ease forwards;
    }
    
    .lang-dropdown a {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.6rem 1rem;
        color: var(--text-dark) !important;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s;
        font-size: 0.9rem;
    }
    
    .lang-dropdown a:hover {
        background: var(--bg-light);
        color: var(--primary-color) !important;
    }
    
    .lang-dropdown a.active {
        background: rgba(0, 51, 102, 0.1);
        color: var(--primary-color) !important;
        font-weight: 600;
    }
    
    .fi {
        border-radius: 4px;
        width: 1.25em;
        }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Mobile Responsive adjustments */
    @media (max-width: 968px) {
        .lang-dropdown-wrapper {
            margin: 1rem 0;
            width: 100%;
        }
        
        .lang-selector {
            color: #1a1a1a !important;
            border-color: #e5e7eb !important;
            justify-content: center;
            width: 100%;
        }
        
        .lang-dropdown {
            position: static;
            width: 100%;
            display: none;
            box-shadow: none;
            background: #f9fafb;
            border: none;
        }
        
        .lang-dropdown.show {
            display: flex;
        }
    }
    
    /* Theme Toggle Button */
    .theme-toggle-btn {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 50%;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .theme-toggle-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(15deg);
    }

    .navbar.scrolled .theme-toggle-btn {
        color: var(--text-dark) !important;
    }

    .navbar.scrolled .theme-toggle-btn:hover {
        background: rgba(0, 0, 0, 0.05);
    }
    
    /* Mobile adjustments */
    @media (max-width: 968px) {
        .theme-toggle-btn {
            color: var(--text-dark);
            width: 100%;
            justify-content: flex-start;
            padding: 1rem;
            border-radius: 0;
        }
        
        .theme-toggle-btn:hover {
            background: rgba(0, 0, 0, 0.05);
            transform: none;
            color: var(--primary-color);
        }
    }
    </style>

    <script>
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    }

    function updateThemeIcon(theme) {
        const icon = document.getElementById('themeIcon');
        if (icon) {
            icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        }
    }

    // Apply theme on load
    (function() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        
        // Wait for DOM to update icon
        document.addEventListener('DOMContentLoaded', () => {
            updateThemeIcon(savedTheme);
            
            // Handle specific styles for non-hero pages if needed
            const btn = document.querySelector('.theme-toggle-btn');
            if (btn && document.querySelector('.navbar').style.background) {
                // If inline style exists (noHero pages), force color
            }
        });
    })();
    </script>
    
    <!-- Main Content -->
    <main>
