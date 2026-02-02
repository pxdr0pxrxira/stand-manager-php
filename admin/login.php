<?php
/**
 * ============================================
 * Admin - Página de Login
 * ============================================
 * Sistema de autenticação seguro com password_verify
 */

session_start();

require_once __DIR__ . '/../config/database.php';

// Se já está autenticado, redirecionar para dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$username = '';

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validar campos
    if (empty($username) || empty($password)) {
        $error = 'Por favor, preencha todos os campos.';
    } else {
        try {
            // Buscar utilizador pelo username
            $users = dbQuery(
                "SELECT id, username, password_hash FROM admins WHERE username = :username LIMIT 1",
                [':username' => $username]
            );
            
            if (!empty($users) && password_verify($password, $users[0]['password_hash'])) {
                // Login bem sucedido
                session_regenerate_id(true); // Prevenir session fixation
                
                $_SESSION['admin_id'] = $users[0]['id'];
                $_SESSION['admin_username'] = $users[0]['username'];
                $_SESSION['last_regeneration'] = time();
                
                // Redirecionar para página original ou dashboard
                $redirect = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
                unset($_SESSION['redirect_after_login']);
                
                header('Location: ' . $redirect);
                exit;
            } else {
                // Credenciais inválidas
                $error = 'Username ou password incorretos.';
                
                // Log para segurança (tentativa falhada)
                error_log('Login falhado para username: ' . $username . ' - IP: ' . $_SERVER['REMOTE_ADDR']);
            }
        } catch (Exception $e) {
            error_log('Erro no login: ' . $e->getMessage());
            $error = 'Ocorreu um erro. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Admin - Stand Automóvel</title>
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #003366;
            --primary-dark: #002244;
            --primary-light: #FF8A50;
            --text-dark: #1a1a1a;
            --text-gray: #6b7280;
            --bg-light: #f9fafb;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated Background Pattern */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(242, 101, 34, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(242, 101, 34, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(255, 138, 80, 0.06) 0%, transparent 50%);
            animation: backgroundMove 20s ease-in-out infinite;
        }
        
        @keyframes backgroundMove {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-5%, -5%); }
        }
        
        /* Floating Shapes */
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.05;
            animation: float 15s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 300px;
            height: 300px;
            background: var(--primary-color);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 200px;
            height: 200px;
            background: var(--primary-light);
            bottom: 15%;
            right: 15%;
            animation-delay: 3s;
        }
        
        .shape:nth-child(3) {
            width: 150px;
            height: 150px;
            background: var(--primary-color);
            top: 60%;
            left: 70%;
            animation-delay: 6s;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
        }
        
        .login-container {
            width: 100%;
            max-width: 480px;
            padding: 2rem;
            position: relative;
            z-index: 10;
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3.5rem 3rem;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 30px 80px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.1);
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            box-shadow: 0 10px 30px rgba(242, 101, 34, 0.3);
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 10px 30px rgba(242, 101, 34, 0.3); }
            50% { transform: scale(1.05); box-shadow: 0 15px 40px rgba(242, 101, 34, 0.4); }
        }
        
        .login-logo-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .login-logo h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
            background: linear-gradient(135deg, var(--text-dark) 0%, var(--text-gray) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .login-logo p {
            color: var(--text-gray);
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.95rem;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-gray);
            font-size: 1.1rem;
            transition: color 0.3s ease;
            z-index: 2;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            color: var(--text-dark);
            font-family: 'Inter', sans-serif;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(242, 101, 34, 0.1);
        }
        
        .form-control:focus + .input-icon {
            color: var(--primary-color);
        }
        
        .form-control::placeholder {
            color: var(--text-light);
        }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(242, 101, 34, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(242, 101, 34, 0.4);
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .alert i {
            font-size: 1.25rem;
        }
        
        .back-link {
            text-align: center;
            margin-top: 2rem;
        }
        
        .back-link a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }
        
        .back-link a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-5px);
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-card {
                padding: 2.5rem 2rem;
            }
            
            .login-logo h1 {
                font-size: 1.5rem;
            }
            
            .shape {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Shapes -->
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-logo">
                <div class="login-logo-icon">
                    <i class="bi bi-car-front-fill"></i>
                </div>
                <h1>Stand Automóvel</h1>
                <p>Área de Administração</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" autocomplete="off">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-wrapper">
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               value="<?php echo htmlspecialchars($username); ?>"
                               placeholder="Introduza o seu username"
                               required 
                               autofocus>
                        <i class="bi bi-person input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password"
                               placeholder="Introduza a sua password"
                               required>
                        <i class="bi bi-lock input-icon"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Entrar</span>
                </button>
            </form>
        </div>
        
        <div class="back-link">
            <a href="../public/index.php">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar ao Website</span>
            </a>
        </div>
    </div>
</body>
</html>
