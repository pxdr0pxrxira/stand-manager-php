<?php
/**
 * Admin Header - Estilo próprio do backoffice
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$adminName = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : ''; ?>Admin - Stand Automóvel</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-color: #003366;
            --primary-dark: #002244;
            --text-dark: #1a1a1a;
            --text-gray: #6b7280;
            --bg-light: #f9fafb;
            --sidebar-bg: #1a1a2e;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
        }
        
        /* Admin Navbar */
        .admin-navbar {
            background: white;
            padding: 0.75rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border-bottom: 1px solid #e5e7eb;
        }
        
        .admin-navbar .container-fluid {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-dark);
            text-decoration: none;
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .admin-brand i {
            font-size: 1.75rem;
            color: var(--primary-color);
        }
        
        /* Logout Button */
        .btn-logout {
            background: #fee2e2;
            color: #dc2626;
            text-decoration: none;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .btn-logout:hover {
            background: #dc2626;
            color: white;
        }

        /* Sidebar & Layout */
        .admin-layout {
            display: flex;
            min-height: calc(100vh - 65px);
        }

        .admin-sidebar {
            width: 260px;
            background: white;
            border-right: 1px solid #e5e7eb;
            padding: 1.5rem 1rem;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .sidebar-section-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 1rem;
            margin-bottom: 0.75rem;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-item {
            margin-bottom: 0.25rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.8rem 1rem;
            color: var(--text-gray);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            font-size: 1.25rem;
            width: 24px;
            text-align: center;
        }

        .sidebar-link:hover {
            color: var(--primary-color);
            background: rgba(0, 51, 102, 0.05);
        }

        .sidebar-link.active {
            color: white;
            background: var(--primary-color);
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.2);
        }

        .admin-nav-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-right: 1.5rem;
            border-right: 1px solid #e5e7eb;
        }

        .navbar-user-avatar {
            width: 32px;
            height: 32px;
            background: var(--primary-color);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .navbar-user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .admin-main-content {
            flex-grow: 1;
            padding: 2.5rem;
            background: #f9fafb;
            min-width: 0; /* Prevent flex children from overflowing */
            display: flex;
            flex-direction: column;
        }
        
        /* Main Content */
        .admin-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Cards */
        .admin-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        
        .admin-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--bg-light);
        }
        
        .admin-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .admin-card-title i {
            color: var(--primary-color);
        }
        
        /* Tables */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th {
            background: var(--bg-light);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-gray);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .admin-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--bg-light);
            vertical-align: middle;
        }
        
        .admin-table tr:hover {
            background: rgba(0, 51, 102, 0.03);
        }
        
        .admin-table img {
            width: 60px;
            height: 45px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-action {
            padding: 0.4rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.2s ease;
        }
        
        .btn-edit {
            background: #e0f2fe;
            color: #0284c7;
        }
        
        .btn-edit:hover {
            background: #0284c7;
            color: white;
        }
        
        .btn-delete {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            cursor: pointer;
        }
        
        .btn-delete:hover {
            background: #dc2626;
            color: white;
        }
        
        /* Stats */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stat-icon.primary {
            background: rgba(0, 51, 102, 0.1);
            color: var(--primary-color);
        }
        
        .stat-icon.success {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }
        
        .stat-icon.info {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1;
        }
        
        .stat-label {
            color: var(--text-gray);
            font-size: 0.9rem;
        }
        
        /* Forms */
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        
        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
        }
        
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: #15803d;
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }
        
        .page-subtitle {
            color: var(--text-gray);
        }
        
        /* Image Preview */
        .image-preview {
            max-height: 200px;
            border-radius: 12px;
            margin-top: 1rem;
        }
        
        .current-image {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
            border: 3px solid var(--bg-light);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .admin-sidebar {
                width: 80px;
                padding: 1.5rem 0.5rem;
            }
            .sidebar-link span, .sidebar-section-label {
                display: none;
            }
            .sidebar-link {
                justify-content: center;
                padding: 1rem;
            }
        }

        @media (max-width: 768px) {
            .admin-layout {
                flex-direction: column;
            }
            .admin-sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                flex-direction: row;
                overflow-x: auto;
                padding: 0.75rem;
                gap: 1rem;
            }
            .sidebar-link {
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Navbar -->
    <nav class="admin-navbar">
        <div class="container-fluid px-4">
            <a href="dashboard.php" class="admin-brand">
                <i class="bi bi-car-front-fill"></i>
                <span>Stand Admin</span>
            </a>
            
            <div class="admin-nav-actions">
                <div class="navbar-user">
                    <div class="navbar-user-avatar">
                        <?php echo strtoupper(substr($adminName, 0, 1)); ?>
                    </div>
                    <span class="navbar-user-name"><?php echo htmlspecialchars($adminName); ?></span>
                </div>
                
                <a href="logout.php" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Sair</span>
                </a>
            </div>
        </div>
    </nav>
    
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-group">
                <div class="sidebar-section-label">Menu Principal</div>
                <ul class="sidebar-nav">
                    <li class="sidebar-item">
                        <a href="dashboard.php" class="sidebar-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="disponiveis.php" class="sidebar-link <?php echo $currentPage === 'disponiveis' ? 'active' : ''; ?>">
                            <i class="bi bi-car-front"></i>
                            <span>Viaturas Disponíveis</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="vendidos.php" class="sidebar-link <?php echo $currentPage === 'vendidos' ? 'active' : ''; ?>">
                            <i class="bi bi-check-circle"></i>
                            <span>Viaturas Vendidas</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="viatura.php" class="sidebar-link <?php echo ($currentPage === 'viatura' && empty($_GET['id'])) ? 'active' : ''; ?>">
                            <i class="bi bi-plus-circle-fill"></i>
                            <span>Adicionar Viatura</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="hero_images.php" class="sidebar-link <?php echo $currentPage === 'hero_images' ? 'active' : ''; ?>">
                            <i class="bi bi-images"></i>
                            <span>Imagens Hero</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="configuracoes.php" class="sidebar-link <?php echo $currentPage === 'configuracoes' ? 'active' : ''; ?>">
                            <i class="bi bi-gear-fill"></i>
                            <span>Configurações</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-group">
                <div class="sidebar-section-label">Acesso Rápido</div>
                <ul class="sidebar-nav">
                    <li class="sidebar-item">
                        <a href="../public/index.php" target="_blank" class="sidebar-link">
                            <i class="bi bi-box-arrow-up-right"></i>
                            <span>Ver Site</span>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <!-- Content Area -->
        <main class="admin-main-content">
            <div class="admin-content">
