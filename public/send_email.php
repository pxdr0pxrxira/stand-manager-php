<?php
/**
 * ============================================
 * Send Email Handler
 * ============================================
 * Processa o formulário de contacto e envia email
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/settings.php';

// Carregar configurações
$settings = getAllSettings();

// Verificar se é um POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contacto.php?error=' . urlencode('Método inválido'));
    exit;
}

// Sanitizar e validar inputs
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telefone = isset($_POST['telefone']) ? trim($_POST['telefone']) : '';
$assunto = isset($_POST['assunto']) ? trim($_POST['assunto']) : '';
$mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : '';

// Validações básicas
$errors = [];

if (empty($nome)) {
    $errors[] = 'Nome é obrigatório';
}

if (empty($email)) {
    $errors[] = 'Email é obrigatório';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email inválido';
}

if (empty($assunto)) {
    $errors[] = 'Assunto é obrigatório';
}

if (empty($mensagem)) {
    $errors[] = 'Mensagem é obrigatória';
}

// Se houver erros, redirecionar de volta
if (!empty($errors)) {
    $errorMsg = implode(', ', $errors);
    header('Location: contacto.php?error=' . urlencode($errorMsg));
    exit;
}

// Configurar email
$companyEmail = getSetting('company_email', 'info@standautomovel.pt');
$companyName = getSetting('company_name', 'Stand Automóvel');

// Email vai do email da empresa para o próprio email da empresa (notificação)
$to = $companyEmail;
$subject = '[Notificação Website] ' . $assunto;

// Construir corpo do email em HTML
$emailBody = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .field { margin-bottom: 20px; }
        .field-label { font-weight: bold; color: #003366; margin-bottom: 5px; }
        .field-value { background: white; padding: 12px; border-radius: 6px; border-left: 3px solid #003366; }
        .footer { text-align: center; margin-top: 20px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚗 Nova Mensagem de Contacto</h1>
        </div>
        <div class="content">
            <div class="field">
                <div class="field-label">Nome:</div>
                <div class="field-value">' . htmlspecialchars($nome) . '</div>
            </div>
            
            <div class="field">
                <div class="field-label">Email:</div>
                <div class="field-value"><a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a></div>
            </div>
            
            ' . (!empty($telefone) ? '
            <div class="field">
                <div class="field-label">Telefone:</div>
                <div class="field-value"><a href="tel:' . htmlspecialchars($telefone) . '">' . htmlspecialchars($telefone) . '</a></div>
            </div>
            ' : '') . '
            
            <div class="field">
                <div class="field-label">Assunto:</div>
                <div class="field-value">' . htmlspecialchars($assunto) . '</div>
            </div>
            
            <div class="field">
                <div class="field-label">Mensagem:</div>
                <div class="field-value">' . nl2br(htmlspecialchars($mensagem)) . '</div>
            </div>
        </div>
        <div class="footer">
            <p>Esta mensagem foi enviada através do formulário de contacto do website ' . htmlspecialchars($companyName) . '</p>
            <p>Data: ' . date('d/m/Y H:i:s') . '</p>
            <p><strong>Responder para:</strong> ' . htmlspecialchars($email) . '</p>
        </div>
    </div>
</body>
</html>
';

// Headers do email - Email da empresa envia para si própria
$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    'From: ' . $companyName . ' <' . $companyEmail . '>',
    'Reply-To: ' . $email, // Responder vai para o cliente
    'X-Mailer: PHP/' . phpversion()
];

require_once __DIR__ . '/../config/email_sender.php';

// Tentar enviar o email usando SMTP ou fallback para arquivo
$mailSent = sendEmailSMTP(
    $to,                    // Destinatário (email da empresa)
    $subject,               // Assunto
    $emailBody,             // Corpo HTML
    $email,                 // Reply-To (email do cliente)
    $nome                   // Nome do cliente
);

if ($mailSent) {
    // Sucesso - redirecionar com mensagem de sucesso
    header('Location: contacto.php?success=1');
    exit;
} else {
    // Erro ao enviar - redirecionar com mensagem de erro
    header('Location: contacto.php?error=' . urlencode('Erro ao enviar email. Por favor, tente novamente ou contacte-nos por telefone.'));
    exit;
}
?>
