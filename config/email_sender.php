<?php
/**
 * ============================================
 * Email Sender with SMTP Support
 * ============================================
 * Função para enviar emails usando SMTP ou fallback para arquivo
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Envia email usando SMTP ou salva em arquivo
 * 
 * @param string $to Email destinatário
 * @param string $subject Assunto
 * @param string $body Corpo do email (HTML)
 * @param string $replyTo Email para responder
 * @param string $replyToName Nome para responder
 * @return bool True se enviado com sucesso
 */
function sendEmailSMTP($to, $subject, $body, $replyTo = '', $replyToName = '') {
    // Carregar configurações
    require_once __DIR__ . '/settings.php';
    $settings = getAllSettings();
    
    $smtpEnabled = ($settings['smtp_enabled'] ?? '0') == '1';
    
    // Se SMTP não está ativado, salvar em arquivo
    if (!$smtpEnabled) {
        return saveEmailToFile($to, $subject, $body, $replyTo, $replyToName);
    }
    
    // Verificar se PHPMailer existe
    $phpmailerPath = __DIR__ . '/../vendor/autoload.php';
    if (!file_exists($phpmailerPath)) {
        // Fallback para arquivo se PHPMailer não estiver instalado
        error_log('PHPMailer não encontrado, salvando email em arquivo');
        return saveEmailToFile($to, $subject, $body, $replyTo, $replyToName);
    }
    
    require $phpmailerPath;
    
    try {
        $mail = new PHPMailer(true);
        
        // Configurações do servidor SMTP
        $mail->isSMTP();
        $mail->Host = $settings['smtp_host'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $settings['smtp_username'] ?? '';
        $mail->Password = $settings['smtp_password'] ?? '';
        $mail->SMTPSecure = ($settings['smtp_encryption'] ?? 'tls') == 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = intval($settings['smtp_port'] ?? 587);
        $mail->CharSet = 'UTF-8';
        
        // Remetente
        $companyEmail = $settings['company_email'] ?? 'info@standautomovel.pt';
        $companyName = $settings['company_name'] ?? 'Stand Automóvel';
        $mail->setFrom($companyEmail, $companyName);
        
        // Destinatário
        $mail->addAddress($to);
        
        // Reply-To (se fornecido)
        if (!empty($replyTo)) {
            $mail->addReplyTo($replyTo, $replyToName);
        }
        
        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body);
        
        // Enviar
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Se falhar, salvar em arquivo
        error_log("Erro ao enviar email via SMTP: {$mail->ErrorInfo}");
        return saveEmailToFile($to, $subject, $body, $replyTo, $replyToName);
    }
}

/**
 * Salva email em arquivo de log
 */
function saveEmailToFile($to, $subject, $body, $replyTo = '', $replyToName = '') {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . '/contact_messages.txt';
    
    // Extrair informações do corpo HTML (se possível)
    $plainBody = strip_tags($body);
    
    $logContent = sprintf(
        "==============================================\n" .
        "Data: %s\n" .
        "Para: %s\n" .
        "Assunto: %s\n" .
        "Responder para: %s (%s)\n" .
        "Mensagem:\n%s\n" .
        "==============================================\n\n",
        date('d/m/Y H:i:s'),
        $to,
        $subject,
        $replyTo,
        $replyToName,
        $plainBody
    );
    
    file_put_contents($logFile, $logContent, FILE_APPEND);
    return true;
}
?>
