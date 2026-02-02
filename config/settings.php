<?php
/**
 * ============================================
 * Settings Helper Functions
 * ============================================
 * Funções para buscar e usar configurações do sistema
 */

/**
 * Buscar todas as configurações do sistema
 * @return array Array associativo com as configurações
 */
function getAllSettings() {
    static $cachedSettings = null;
    
    if ($cachedSettings !== null) {
        return $cachedSettings;
    }
    
    try {
        $settingsRaw = dbQuery("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        foreach ($settingsRaw as $setting) {
            $settings[$setting['setting_key']] = $setting['setting_value'];
        }
        $cachedSettings = $settings;
        return $settings;
    } catch (Exception $e) {
        // Retornar valores padrão em caso de erro
        return [
            'company_name' => 'Stand Automóvel',
            'company_email' => 'info@standautomovel.pt',
            'company_phone' => '+351 912 345 678',
            'company_address' => 'Rua Principal, 123',
            'company_city' => '1000-001 Lisboa, Portugal',
            'company_hours' => "Segunda a Sexta: 9h - 19h\nSábado: 9h - 13h\nDomingo: Encerrado",
            'whatsapp_number' => '351912345678',
            'facebook_url' => '#',
            'instagram_url' => '#',
            'maps_latitude' => '38.7223',
            'maps_longitude' => '-9.1393',
            'maps_embed_url' => ''
        ];
    }
}

/**
 * Buscar uma configuração específica
 * @param string $key Chave da configuração
 * @param mixed $default Valor padrão se não encontrado
 * @return mixed Valor da configuração
 */
function getSetting($key, $default = '') {
    $settings = getAllSettings();
    return $settings[$key] ?? $default;
}

/**
 * Obter URL do WhatsApp formatado
 * @return string URL do WhatsApp
 */
function getWhatsAppUrl() {
    $number = getSetting('whatsapp_number', '351912345678');
    return 'https://wa.me/' . $number;
}

/**
 * Obter URL do Google Maps Directions
 * @return string URL para direções
 */
function getMapsDirectionsUrl() {
    $lat = getSetting('maps_latitude', '38.7223');
    $lng = getSetting('maps_longitude', '-9.1393');
    return "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lng}";
}

/**
 * Formatar horário com quebras de linha HTML
 * @return string Horário formatado
 */
function getFormattedHours() {
    $hours = getSetting('company_hours', '');
    return nl2br(htmlspecialchars($hours));
}
?>
